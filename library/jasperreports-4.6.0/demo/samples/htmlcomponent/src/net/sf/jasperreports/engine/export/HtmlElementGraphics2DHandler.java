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

import java.awt.Graphics2D;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.export.JRGraphics2DExporterContext;
import net.sf.jasperreports.engine.export.draw.ImageDrawer;
import net.sf.jasperreports.engine.export.draw.Offset;
import net.sf.jasperreports.engine.util.HtmlPrintElement;
import net.sf.jasperreports.engine.util.HtmlPrintElementUtils;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: HtmlElementGraphics2DHandler.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class HtmlElementGraphics2DHandler implements GenericElementGraphics2DHandler {

	public boolean toExport(JRGenericPrintElement element) {
		return true;
	}

	public void exportElement(JRGraphics2DExporterContext exporterContext, JRGenericPrintElement element, Graphics2D grx, Offset offset) {
		HtmlPrintElement htmlPrintElement;
		try {
			htmlPrintElement = HtmlPrintElementUtils.getHtmlPrintElement();
			
			JRGraphics2DExporter exporter = (JRGraphics2DExporter)exporterContext.getExporter();
			ImageDrawer imageDrawer = exporter.getFrameDrawer().getDrawVisitor().getImageDrawer();
			
			imageDrawer.draw(
					grx,
					htmlPrintElement.createImageFromElement(element), 
					offset.getX(), 
					offset.getY()
					);
			
		} catch (JRException e) {
			throw new RuntimeException(e);
		}

	}
	
}
