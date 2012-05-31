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
package net.sf.jasperreports.engine.query;

import java.util.Map;

import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRValueParameter;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * XPath query executer factory.
 * <p/>
 * The factory creates {@link net.sf.jasperreports.engine.query.JRXPathQueryExecuter JRXPathQueryExecuter}
 * query executers.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRXPathQueryExecuterFactory.java 5305 2012-04-26 15:17:33Z teodord $
 */
public class JRXPathQueryExecuterFactory extends AbstractQueryExecuterFactory
{
	/**
	 * Built-in parameter holding the value of the org.w3c.dom.Document used to run the XPath query.
	 */
	public final static String PARAMETER_XML_DATA_DOCUMENT = "XML_DATA_DOCUMENT";
	
	/**
	 * Built-in parameter holding the value of the <code>java.io.InputStream</code> to be used for obtaining the XML data.
	 */
	public static final String XML_INPUT_STREAM = "XML_INPUT_STREAM";
	
	/**
	 * Built-in parameter holding the value of the <code>java.io.File</code> to be used for obtaining the XML data.
	 */
	public static final String XML_FILE = "XML_FILE";
	
	/**
	 * Built-in parameter/property holding the value of the <code>java.lang.String</code> source to be used for obtaining the XML data.
	 */
	public static final String XML_SOURCE = JRPropertiesUtil.PROPERTY_PREFIX + "xml.source";
	
	/**
	 * Parameter holding the format pattern used to instantiate java.util.Date instances.
	 */
	public final static String XML_DATE_PATTERN = "XML_DATE_PATTERN";
	
	/**
	 * Property holding the value of the date format pattern to be used when parsing the XML data.
	 */
	public static final String PROPERTY_XML_DATE_PATTERN = JRPropertiesUtil.PROPERTY_PREFIX + "xml.date.pattern";

	/**
	 * Parameter holding the format pattern used to instantiate java.lang.Number instances.
	 */
	public final static String XML_NUMBER_PATTERN = "XML_NUMBER_PATTERN";

	/**
	 * Property holding the value of the number format pattern to be used when parsing the XLS data.
	 */
	public static final String PROPERTY_XML_NUMBER_PATTERN = JRPropertiesUtil.PROPERTY_PREFIX + "xml.number.pattern";
	
	/**
	 * Parameter holding the value of the datasource Locale
	 */
	public final static String XML_LOCALE = "XML_LOCALE";//FIXME make properties for locale and timezone too; just like in csv
	
	/**
	 * Parameter holding the value of the datasource Timezone
	 */
	public final static String XML_TIME_ZONE = "XML_TIME_ZONE";
	
	private final static Object[] XPATH_BUILTIN_PARAMETERS = {
		PARAMETER_XML_DATA_DOCUMENT,  "org.w3c.dom.Document",
		XML_INPUT_STREAM, "java.io.InputStream",
		XML_FILE, "java.io.File",
		XML_SOURCE, "java.lang.String",
		XML_DATE_PATTERN, "java.lang.String",
		XML_NUMBER_PATTERN, "java.lang.String",
		XML_LOCALE, "java.util.Locale",
		XML_TIME_ZONE, "java.util.TimeZone",
		};

	public Object[] getBuiltinParameters()
	{
		return XPATH_BUILTIN_PARAMETERS;
	}

	public JRQueryExecuter createQueryExecuter(
		JasperReportsContext jasperReportsContext, 
		JRDataset dataset, 
		Map<String,? extends JRValueParameter> parameters
		) throws JRException
	{
		return new JRXPathQueryExecuter(jasperReportsContext, dataset, parameters);
	}

	public boolean supportsQueryParameterType(String className)
	{
		return true;
	}
}
