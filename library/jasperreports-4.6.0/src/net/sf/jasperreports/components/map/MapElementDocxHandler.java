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
package net.sf.jasperreports.components.map;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.export.JRExporterGridCell;
import net.sf.jasperreports.engine.export.ooxml.GenericElementDocxHandler;
import net.sf.jasperreports.engine.export.ooxml.JRDocxExporter;
import net.sf.jasperreports.engine.export.ooxml.JRDocxExporterContext;

/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: MapElementDocxHandler.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class MapElementDocxHandler implements GenericElementDocxHandler
{
	private static final MapElementDocxHandler INSTANCE = new MapElementDocxHandler();
	
	public static MapElementDocxHandler getInstance()
	{
		return INSTANCE;
	}
	
	public void exportElement(
		JRDocxExporterContext exporterContext,
		JRGenericPrintElement element,
		JRExporterGridCell gridCell
		)
	{
		try
		{
			JRDocxExporter exporter = (JRDocxExporter)exporterContext.getExporter();
			exporter.exportImage(exporterContext.getTableHelper(), getImage(exporterContext, element), gridCell);
		}
		catch (Exception e)
		{
			throw new RuntimeException(e);
		}
	}

	public boolean toExport(JRGenericPrintElement element) {
		return true;
	}

	public JRPrintImage getImage(JRDocxExporterContext exporterContext, JRGenericPrintElement element) throws JRException
	{
		return MapElementImageProvider.getImage(exporterContext.getJasperReportsContext(), element);
	}

}
