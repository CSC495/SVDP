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
package net.sf.jasperreports.engine.export;

import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.util.HtmlPrintElement;
import net.sf.jasperreports.engine.util.HtmlPrintElementUtils;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: HtmlElementPdfHandler.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class HtmlElementPdfHandler implements GenericElementPdfHandler 
{
	public void exportElement(JRPdfExporterContext exporterContext,
			JRGenericPrintElement element) {
		try
		{
			HtmlPrintElement htmlPrintElement = HtmlPrintElementUtils.getHtmlPrintElement();
			((JRPdfExporter)exporterContext.getExporter()).exportImage(htmlPrintElement.createImageFromElement(element));
		}
		catch (Exception e)
		{
			throw new RuntimeException(e);
		}
	}
	
	public boolean toExport(JRGenericPrintElement element) 
	{
		return true;
	}
	
}
