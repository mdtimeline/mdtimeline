<?php
/**
 * Created by IntelliJ IDEA.
 * User: ernesto
 * Date: 9/8/18
 * Time: 8:29 PM
 */

include_once (ROOT. '/classes/Network.php');
include_once (ROOT. '/dataProvider/Patient.php');
include_once (ROOT. '/dataProvider/User.php');
include_once (ROOT. '/dataProvider/CombosData.php');
include_once (ROOT. '/dataProvider/Documents.php');
include_once (ROOT. '/dataProvider/DocumentHandler.php');

class LegalLetters
{

	/**
	 * @var MatchaCUP
	 */
	private $l;
	/**
	 * @var MatchaCUP
	 */
	private $s;

	private $CombosData;
	private $Documents;
	private $DocumentHandler;
	private $Patient;
	private $User;

	function __construct(){
		$this->l = MatchaModel::setSenchaModel('App.model.administration.LegalLetter');
		$this->s = MatchaModel::setSenchaModel('App.model.administration.LegalLetterSignature');
		$this->Patient = new Patient();
		$this->User = new User();
		$this->CombosData = new CombosData();
		$this->Documents = new Documents();
		$this->DocumentHandler = new DocumentHandler();
	}

	public function getLegalLetters($params){
		return $this->l->load($params)->all();
	}

	public function getLegalLetter($params){
		return $this->l->load($params)->one();
	}

	public function addLegalLetter($params){
		return $this->l->save($params);
	}

	public function updateLegalLetter($params){
		return $this->l->save($params);
	}

	public function destroyLegalLetter($params){
		return $this->l->destroy($params);
	}


    public function getLegalLetterSignature($params){
        return $this->s->load($params)->leftJoin(
            [
                'title' => 'letter_title',
                'content' => 'letter_content',
            ],
            'legal_letters',
            'letter_id',
            'id'
        )->one();
    }

    public function getLegalLetterSignatures($params){
        return $this->s->load($params)->leftJoin(
            [
                'title' => 'letter_title',
                'content' => 'letter_content',
            ],
            'legal_letters',
            'letter_id',
            'id'
        )->all();
    }

    public function addLegalLetterSignature($params){
        return $this->s->save($params);
    }

    public function updateLegalLetterSignature($params){
        return $this->s->save($params);
    }

    public function destroyLegalLetterSignature($params){
        return $this->s->destroy($params);
    }

    public function doSignDocuments($documents, $signature){

        $now = date('Y-m-d H:i:s');
        $ip = \Network::getIpAddress();

        foreach ($documents as $document){

            $signature_object = (object)[
                'pid' => $document->pid,
                'letter_id' => $document->id,
                'letter_version' => $document->version,
                'letter_content' => $document->content,
                'facility_id' => $document->facility_id,
                'document_code' => $document->document_code,
                'signature' => $signature,
                'signature_ip' => $ip,
                'signature_date' => $now,
                'signature_hash' => hash('sha256' , $signature.$ip.$now)
            ];

            $document_id = $this->generatePdfDocument($signature_object);

            $signature_object->document_id = $document_id;

            $this->addLegalLetterSignature($signature_object);

        }
    }

    public function doPreviewDocuments($documents, $signature){
        $now = date('Y-m-d H:i:s');
        $ip = \Network::getIpAddress();

        $document_ids = [];

        foreach ($documents as $document){

            $signature_object = (object)[
                'pid' => $document->pid,
                'letter_id' => $document->id,
                'letter_version' => $document->version,
                'letter_content' => $document->content,
                'facility_id' => $document->facility_id,
                'document_code' => $document->document_code,
                'signature' => $signature,
                'signature_ip' => $ip,
                'signature_date' => $now,
                'signature_hash' => hash('sha256' , $signature.$ip.$now)
            ];

            $document_ids[] = $this->generatePdfDocument($signature_object, true);

        }

        return $document_ids;

    }

    public function generatePdfDocument($signature_object, $is_previews = false){

        $params = (object)[
            'pid' => $signature_object->pid,
            'facility_id' => $signature_object->facility_id,
            'body' => $this->replaceTokens($signature_object->pid, $signature_object->letter_content, $signature_object->signature)
        ];

        $pdf_data = $this->Documents->PDFDocumentBuilder($params);
        $doc_type = $this->CombosData->getDisplayValueByListKeyAndOptionValue('doc_type_cat', $signature_object->document_code);

        if($is_previews){

            $document = $this->DocumentHandler->createTempDocument((object)[
                'document_name' => 'Legal-Letter.pdf',
                'facility_id' => $signature_object->facility_id,
                'document' => base64_encode($pdf_data)
            ]);

            return $document->id;

        }else{
            $document = $this->DocumentHandler->addPatientDocument((object)[
                'pid' => $signature_object->pid,
                'eid' => 0,
                'uid' => 0,
                'facility_id' => $signature_object->facility_id,
                'docType' => $doc_type,
                'docTypeCode' => $signature_object->document_code,
                'title' => '',
                'date' => date('Y-m-d H:i:s'),
                'document' => $pdf_data,
                'encrypted' => false,
                'entered_in_error' => false,
                'error_note' => '',
            ]);

            return $document['data']->id;

        }



    }

	public function getLegalLettersToSignByPid($pid, $facility_id, $workflow){

	    $sql = "SELECT pl.* FROM (
                    SELECT	letter.*,
                            MIN(datediff(CURDATE(), signature.signature_date)) AS signed_days_ago
                    FROM legal_letters AS letter
                    LEFT JOIN legal_letters_signatures AS signature ON
                        signature.letter_id = letter.id AND
                        letter.workflow = :workflow AND
                        signature.pid = :pid AND 
                        (letter.facility_id = :facility_id OR letter.facility_id = '0')
                    GROUP BY letter.id
                ) as pl
                WHERE pl.days_valid_for <= pl.signed_days_ago OR pl.signed_days_ago IS NULL";

	    return $this->l->sql($sql)->all([':facility_id'=> $facility_id,':pid' => $pid, ':workflow' => $workflow]);
	}

	public function replaceTokens($pid, $letter_content, $signature){

	    $tokens_data = $this->Patient->getPatientTokenByPid($pid);
	    $tokens_data = array_merge($tokens_data, $this->User->getUserTokenById());

        $signature_image_data = preg_replace('#^data:image/[^;]+;base64,#', '', $signature);

        $tokens_data['[DATE]'] = date('Y-m-d');
        $tokens_data['[TIME]'] = date('H:i:s');
        $tokens_data['[FORMATTED_DATE]'] = date('F j, Y');
        $tokens_data['[FORMATTED_TIME]'] = date('g:i a');
        $tokens_data['[SIGNATURE]'] = '<img src="@' . $signature_image_data . '" width="90">';

	    $tokens = array_keys($tokens_data);
	    $values = array_values($tokens_data);

        $letter_content = str_replace($tokens, $values, $letter_content);

	    return $letter_content;
    }
}


