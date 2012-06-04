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
package net.sf.jasperreports.data.xml;

import java.io.IOException;
import java.io.InputStream;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;

import net.sf.jasperreports.data.AbstractDataAdapterService;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.data.JRXmlDataSource;
import net.sf.jasperreports.engine.query.JRXPathQueryExecuterFactory;
import net.sf.jasperreports.engine.util.JRXmlUtils;
import net.sf.jasperreports.repo.RepositoryUtil;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.w3c.dom.Document;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: XmlDataAdapterService.java 5346 2012-05-08 12:08:01Z teodord $
 */
public class XmlDataAdapterService extends AbstractDataAdapterService
{

	private static final Log log = LogFactory.getLog(XmlDataAdapterService.class);

	/**
	 * 
	 */
	public XmlDataAdapterService(
		JasperReportsContext jasperReportsContext,
		XmlDataAdapter xmlDataAdapter
		) 
	{
		super(jasperReportsContext, xmlDataAdapter);
	}
	
	/**
	 * @deprecated Replaced by {@link #XmlDataAdapterService(JasperReportsContext, XmlDataAdapter)}.
	 */
	public XmlDataAdapterService(XmlDataAdapter xmlDataAdapter) 
	{
		this(DefaultJasperReportsContext.getInstance(), xmlDataAdapter);
	}
	
	public XmlDataAdapter getXmlDataAdapter()
	{
		return (XmlDataAdapter)getDataAdapter();
	}
	
	@Override
	public void contributeParameters(Map<String, Object> parameters) throws JRException
	{
		XmlDataAdapter xmlDataAdapter = getXmlDataAdapter();
		if (xmlDataAdapter != null)
		{
			if (xmlDataAdapter.isUseConnection()) {
				
				/*
				if (this.getFilename().toLowerCase().startsWith("https://") ||
					this.getFilename().toLowerCase().startsWith("http://") ||
					this.getFilename().toLowerCase().startsWith("file:"))
				{
					map.put(JRXPathQueryExecuterFactory.XML_URL, this.getFilename());
				}
				else
				{
				*/
					InputStream dataStream = RepositoryUtil.getInstance(getJasperReportsContext()).getInputStreamFromLocation(xmlDataAdapter.getFileName());
					try
					{
						Document document = JRXmlUtils.parse(dataStream);
						parameters.put(JRXPathQueryExecuterFactory.PARAMETER_XML_DATA_DOCUMENT, document);
					}
					finally
					{
						try
						{
							dataStream.close();
						}
						catch (IOException e)
						{
							log.warn("Failed to close input stream for " + xmlDataAdapter.getFileName());
						}
					}
				//}
				
				
				Locale locale = xmlDataAdapter.getLocale();
				if (locale != null) {
					parameters.put(JRXPathQueryExecuterFactory.XML_LOCALE, locale);
				}

				TimeZone timeZone = xmlDataAdapter.getTimeZone();
				if (timeZone != null) {
					parameters.put(JRXPathQueryExecuterFactory.XML_TIME_ZONE, timeZone);
				}

				String datePattern = xmlDataAdapter.getDatePattern();
				if (datePattern != null && datePattern.trim().length() > 0) {
					parameters.put(JRXPathQueryExecuterFactory.XML_DATE_PATTERN, datePattern);
				}

				String numberPattern = xmlDataAdapter.getNumberPattern();
				if (numberPattern != null && numberPattern.trim().length() > 0) {
					parameters.put(JRXPathQueryExecuterFactory.XML_NUMBER_PATTERN, numberPattern);
				}	
			}
			else
			{
				JRXmlDataSource ds = new JRXmlDataSource(getJasperReportsContext(), xmlDataAdapter.getFileName(), xmlDataAdapter.getSelectExpression()); 

				Locale locale = xmlDataAdapter.getLocale();
				if (locale != null) {
					ds.setLocale(locale);
				}

				TimeZone timeZone = xmlDataAdapter.getTimeZone();
				if (timeZone != null) {
					ds.setTimeZone(timeZone);
				}
				
				String datePattern = xmlDataAdapter.getDatePattern();
				if (datePattern != null && datePattern.trim().length() > 0) {
					ds.setDatePattern(datePattern);
				}

				String numberPattern = xmlDataAdapter.getNumberPattern();
				if (numberPattern != null && numberPattern.trim().length() > 0) {
					ds.setNumberPattern(numberPattern);
				}	

				parameters.put(JRParameter.REPORT_DATA_SOURCE, ds);
			}
		}
	}
	
}
