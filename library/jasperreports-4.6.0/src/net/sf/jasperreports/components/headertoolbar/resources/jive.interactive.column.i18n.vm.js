jQuery.extend(jive.i18n.keys, {
		'column.format.dialog.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.formatcolumn')",
		'column.format.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.format')",
		'column.format.formatmenu.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.formatting')",
		'column.format.hidecolumn.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.hidecolumn')",
		'column.format.showcolumns.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.showcolumns')",
		'column.format.showcolumns.all.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.showcolumns.all')",
		
		'column.filter.dialog.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.filtercolumn')",
		'column.filter.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.columnfilters')",
		'column.sortasc.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.sortasc')",
		'column.sortdesc.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.sortdesc')",

		'column.dialog.extfonts': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.extfonts')",
		'column.dialog.sysfonts': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.sysfonts')",
		
		'column.filterform.clearfilter.true.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.clearfilter.true')",
		'column.filterform.clearfilter.false.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.clearfilter.false')",

		'column.formatHeaderForm.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.headings')",
		'column.formatHeaderForm.headingName.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.headingtext')",

		'column.formatforms.fontName.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.fontname')",
		'column.formatforms.fontSize.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.fontsize')",
		'column.formatforms.fontColor.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.fontcolor')",
		'column.formatforms.styleButtons.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.style')",
		
		'column.formatCellsForm.title': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.title.values')",
		'column.formatCellsForm.formatPattern.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.formatpattern')",
		'column.formatCellsForm.numberFormatButtons.localespecific.label': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.localespecific')",
		
		'column.move.helper': "$msgProvider.getMessage('net.sf.jasperreports.components.headertoolbar.label.draghelper')"
});

jasperreports.events.registerEvent('jive.interactive.column.i18n.init').trigger();