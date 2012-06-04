/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2001 - 2011 Jaspersoft Corporation. All rights reserved.
 * http://www.jaspersoft.com
 *
 * Unless you have purchased a commercial license agreement from Jaspersoft,
 * the following license terms apply:
 *
 * This program is part of JasperReports.
 *
 * JasperReports is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * JasperReports is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with JasperReports. If not, see <http://www.gnu.org/licenses/>.
 */
package net.sf.jasperreports.components.ofc;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.export.JRExporterGridCell;
import net.sf.jasperreports.engine.export.ooxml.GenericElementXlsxHandler;
import net.sf.jasperreports.engine.export.ooxml.JRXlsxExporter;
import net.sf.jasperreports.engine.export.ooxml.JRXlsxExporterContext;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ChartXlsxHandler.java 5155 2012-03-28 11:50:29Z teodord $
 */
public class ChartXlsxHandler extends BaseChartHandler implements GenericElementXlsxHandler
{
	public void exportElement(
		JRXlsxExporterContext exporterContext,
		JRGenericPrintElement element,
		JRExporterGridCell gridCell,
		int colIndex,
		int rowIndex
		) throws JRException
	{
		JRXlsxExporter exporter = (JRXlsxExporter)exporterContext.getExporter();
		
		JRExporterGridCell newGridCell = getGridCellReplacement(exporterContext, element, gridCell); 
		
		exporter.exportText((JRPrintText)newGridCell.getElement(), newGridCell, colIndex, rowIndex);
	}

	@Override
	public JRPrintImage getImage(JRXlsxExporterContext exporterContext, JRGenericPrintElement element) throws JRException 
	{
		return null;
	}
}
