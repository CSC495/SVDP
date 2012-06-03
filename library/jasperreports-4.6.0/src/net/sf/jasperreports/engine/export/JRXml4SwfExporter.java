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

/*
 * Contributors:
 * Joakim Sandstr�m - sanjoa@users.sourceforge.net
 */
package net.sf.jasperreports.engine.export;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JasperReportsContext;


/**
 * This is a modified version of the JRXmlExporter class, which produces an XML document that is used by the Flash viewer.
 * 
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRXml4SwfExporter.java 5166 2012-03-28 13:11:05Z teodord $
 */
public class JRXml4SwfExporter extends JRXmlExporter
{
	/**
	 *
	 */
	private static final String XML4SWF_EXPORTER_PROPERTIES_PREFIX = JRPropertiesUtil.PROPERTY_PREFIX + "export.xml4swf.";

	/**
	 * The exporter key, as used in
	 * {@link GenericElementHandlerEnviroment#getHandler(net.sf.jasperreports.engine.JRGenericElementType, String)}.
	 */
	public static final String XML4SWF_EXPORTER_KEY = JRPropertiesUtil.PROPERTY_PREFIX + "xml4swf";
	

	/**
	 * @see #JRXml4SwfExporter(JasperReportsContext)
	 */
	public JRXml4SwfExporter()
	{
		this(DefaultJasperReportsContext.getInstance());
	}

	
	/**
	 *
	 */
	public JRXml4SwfExporter(JasperReportsContext jasperReportsContext)
	{
		super(jasperReportsContext);
	}
	

	/**
	 *
	 */
	protected String getExporterPropertiesPrefix()
	{
		return XML4SWF_EXPORTER_PROPERTIES_PREFIX;
	}

	/**
	 *
	 */
	protected String getExporterKey()
	{
		return XML4SWF_EXPORTER_KEY;
	}
}
