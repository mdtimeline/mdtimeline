/**
 * @class App.ux.grid.exporter.excelFormatter.ExcelFormatter
 * @extends App.ux.grid.exporter.Formatter
 * Specialised Format class for outputting .xls files
 */
Ext.define("App.ux.grid.exporter.excelFormatter.ExcelFormatter", {
    extend: "App.ux.grid.exporter.Formatter",
    uses: [
        "App.ux.grid.exporter.excelFormatter.Cell",
        "App.ux.grid.exporter.excelFormatter.Style",
        "App.ux.grid.exporter.excelFormatter.Worksheet",
        "App.ux.grid.exporter.excelFormatter.Workbook"
    ],
    //contentType: 'data:application/vnd.ms-excel;base64,',
    //contentType: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8",
    //mimeType: "application/vnd.ms-excel",
   	mimeType: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
   	//charset:"base64",
    charset:"UTF-8",
    extension: "xls",
	
    format: function(store, config) {
      var workbook = new App.ux.grid.exporter.excelFormatter.Workbook(config);
      workbook.addWorksheet(store, config || {});

      return workbook.render();
    }
});