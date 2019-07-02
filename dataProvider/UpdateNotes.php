<?php
/**
 * GaiaEHR (Electronic Health Records)
 * Copyright (C) 2013 Certun, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class UpdateNotes {
    /**
     * @var MatchaCUP
     */
    private $u;

    private $ua;

    function __construct(){
        if(!isset($this->u))
            $this->u = MatchaModel::setSenchaModel('App.model.updateNotes.UpdateNotes');
        if(!isset($this->ua))
            $this->ua = MatchaModel::setSenchaModel('App.model.updateNotesAcknowledge.UpdateNotesAcknowledge');
    }

    public function getLatestUpdate(){
        $sql = "SELECT *
                FROM update_notes 
                WHERE id=(SELECT max(id) FROM update_notes)";

        return $this->u->sql($sql)->one();
    }

    public function getUpdateAcknowledge($notesID, $userId){
        $sql = "SELECT *
                FROM update_notes_acknowledge as acknowledge
                WHERE acknowledge.notes_id = :notesID AND acknowledge.user_id = :userID";



        return $this->ua->sql($sql)->one(['notesID' => $notesID, 'userID' => $userId]);
    }

    public function setUpdateAcknowledge($notesID, $userId){
        return $this->ua->save((Object)['notes_id' => $notesID, 'user_id' => $userId]);
    }


}