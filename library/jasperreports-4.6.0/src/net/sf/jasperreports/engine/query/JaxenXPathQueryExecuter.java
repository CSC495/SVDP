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

import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;

import javax.xml.parsers.DocumentBuilderFactory;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRPropertiesUtil.PropertySuffix;
import net.sf.jasperreports.engine.JRValueParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.data.JaxenXmlDataSource;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.w3c.dom.Document;

/**
 * Jaxen XPath query executer implementation.
 * <p/>
 * The XPath query of the report is executed against the document specified by the
 * {@link net.sf.jasperreports.engine.query.JaxenXPathQueryExecuterFactory#PARAMETER_XML_DATA_DOCUMENT PARAMETER_XML_DATA_DOCUMENT}
 * parameter.
 * <p/>
 * All the parameters in the XPath query are replaced by calling <code>String.valueOf(Object)</code>
 * on the parameter value.
 * 
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JaxenXPathQueryExecuter.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JaxenXPathQueryExecuter extends JRAbstractQueryExecuter
{
	private static final Log log = LogFactory.getLog(JaxenXPathQueryExecuter.class);
	
	private final Document document;
	
	private final DocumentBuilderFactory documentBuilderFactory;
	
	private Map<String, String> namespacesMap;
	
	/**
	 * 
	 */
	public JaxenXPathQueryExecuter(
		JasperReportsContext jasperReportsContext,
		JRDataset dataset, 
		Map<String,? extends JRValueParameter> parametersMap
		)
	{
		super(jasperReportsContext, dataset, parametersMap);
				
		document = (Document) getParameterValue(JaxenXPathQueryExecuterFactory.PARAMETER_XML_DATA_DOCUMENT);
		documentBuilderFactory = (DocumentBuilderFactory) getParameterValue(JaxenXPathQueryExecuterFactory.PARAMETER_DOCUMENT_BUILDER_FACTORY);
		namespacesMap = (Map<String, String>) getParameterValue(JaxenXPathQueryExecuterFactory.PARAMETER_XML_NAMESPACE_MAP);
		
		if (document == null)
		{
			log.warn("The supplied org.w3c.dom.Document object is null.");
		}

		parseQuery();
	}

	/**
	 * @deprecated Replaced by {@link #JaxenXPathQueryExecuter(JasperReportsContext, JRDataset, Map)}.
	 */
	public JaxenXPathQueryExecuter(JRDataset dataset, Map<String,? extends JRValueParameter> parametersMap)
	{
		this(DefaultJasperReportsContext.getInstance(), dataset, parametersMap);
	}

	protected String getParameterReplacement(String parameterName)
	{
		return String.valueOf(getParameterValue(parameterName));
	}

	public JRDataSource createDatasource() throws JRException
	{
		JaxenXmlDataSource datasource = null;
		
		String xPath = getQueryString();
		
		if (log.isDebugEnabled())
		{
			log.debug("XPath query: " + xPath);
		}
		
		if (document != null && xPath != null)
		{
			if (namespacesMap == null)
			{
				namespacesMap = extractXmlNamespacesFromProperties();
			}
			
			datasource = new JaxenXmlDataSource(document, xPath);
			
			datasource.setXmlNamespaceMap(namespacesMap);
			datasource.setDetectXmlNamespaces(getBooleanParameterOrProperty(JaxenXPathQueryExecuterFactory.XML_DETECT_NAMESPACES, false));
			datasource.setDocumentBuilderFactory(documentBuilderFactory);
			
			datasource.setLocale((Locale)getParameterValue(JaxenXPathQueryExecuterFactory.XML_LOCALE, true));
			datasource.setDatePattern((String)getParameterValue(JaxenXPathQueryExecuterFactory.XML_DATE_PATTERN, true));
			datasource.setNumberPattern((String)getParameterValue(JaxenXPathQueryExecuterFactory.XML_NUMBER_PATTERN, true));
			datasource.setTimeZone((TimeZone)getParameterValue(JaxenXPathQueryExecuterFactory.XML_TIME_ZONE, true));
		}
		
		return datasource;
	}

	public void close()
	{
		//nothing to do
	}

	public boolean cancelQuery() throws JRException
	{
		//nothing to cancel
		return false;
	}
	
	private Map<String, String> extractXmlNamespacesFromProperties() throws JRException 
	{
		Map<String, String> namespaces = new HashMap<String, String>();
		
		String xmlnsPrefix = JaxenXPathQueryExecuterFactory.XML_NAMESPACE_PREFIX;
		List<PropertySuffix> nsProperties = JRPropertiesUtil.getProperties(dataset, xmlnsPrefix);
		
		for (int i=0; i < nsProperties.size(); ++i) 
		{
			PropertySuffix prop = nsProperties.get(i);
			String nsPrefix = prop.getKey().substring(xmlnsPrefix.length());
			if (nsPrefix.length() > 0) 
			{
				namespaces.put(nsPrefix, prop.getValue());
			}
		}
		
		return namespaces.size()>0 ? namespaces : null;
	}
}