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

import java.io.File;
import java.io.IOException;
import java.io.InputStream;
import java.io.Reader;
import java.net.URL;
import java.text.DateFormat;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Locale;
import java.util.Map;
import java.util.TimeZone;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataSource;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JRPropertiesUtil.PropertySuffix;
import net.sf.jasperreports.engine.JRValueParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.data.JRCsvDataSource;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * CSV query executer implementation.
 * 
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JRCsvQueryExecuter.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRCsvQueryExecuter extends JRAbstractQueryExecuter 
{
	
	private static final Log log = LogFactory.getLog(JRCsvQueryExecuter.class);
	
	private JRCsvDataSource datasource;
	
	/**
	 * 
	 */
	protected JRCsvQueryExecuter(
		JasperReportsContext jasperReportsContext,
		JRDataset dataset, 
		Map<String, ? extends JRValueParameter> parametersMap
		) 
	{
		super(jasperReportsContext, dataset, parametersMap);
	}

	/**
	 * @deprecated Replaced by {@link #JRCsvQueryExecuter(JasperReportsContext, JRDataset, Map)}. 
	 */
	protected JRCsvQueryExecuter(
		JRDataset dataset, 
		Map<String, ? extends JRValueParameter> parametersMap
		) 
	{
		this(DefaultJasperReportsContext.getInstance(), dataset, parametersMap);
	}

	public JRDataSource createDatasource() throws JRException {
		String csvCharset = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_ENCODING);
		
		try {
			Reader csvReader = (Reader) getParameterValue(JRCsvQueryExecuterFactory.CSV_READER); 
			if (csvReader != null) {
				datasource = new JRCsvDataSource(csvReader);
			} else {
				InputStream csvInputStream = (InputStream) getParameterValue(JRCsvQueryExecuterFactory.CSV_INPUT_STREAM);
				if (csvInputStream != null) {
					if (csvCharset != null) {
						datasource = new JRCsvDataSource(csvInputStream, csvCharset);
					} else {
						datasource = new JRCsvDataSource(csvInputStream);
					}
				} else {
					File csvFile = (File) getParameterValue(JRCsvQueryExecuterFactory.CSV_FILE);
					if (csvFile != null) {
						if (csvCharset != null) {
							datasource = new JRCsvDataSource(csvFile, csvCharset);
						} else {
							datasource = new JRCsvDataSource(csvFile);
						}
					} else {
						URL csvUrl = (URL) getParameterValue(JRCsvQueryExecuterFactory.CSV_URL);
						if (csvUrl != null) {
							if (csvCharset != null) {
								datasource = new JRCsvDataSource(csvUrl, csvCharset);
							} else {
								datasource = new JRCsvDataSource(csvUrl);
							}
						} else {
							String csvSource = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_SOURCE);
							if (csvSource != null) {
								if (csvCharset != null) {
									datasource = new JRCsvDataSource(getJasperReportsContext(), csvSource, csvCharset);
								} else {
									datasource = new JRCsvDataSource(getJasperReportsContext(), csvSource);
								}
							} else {
								if (log.isWarnEnabled()){
									log.warn("No CSV source was provided.");
								}
							}
						}
					}
				}
			}
		} catch (IOException e) {
			throw new JRException(e);
		}
		
		if (datasource != null) {
			List<String> columnNamesList = null;
			String columnNames = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_COLUMN_NAMES);
			if(columnNames != null) {
				columnNamesList = new ArrayList<String>();
				columnNamesList.add(columnNames);
			} else {
				String[] columnNamesArray = (String[]) getParameterValue(JRCsvQueryExecuterFactory.CSV_COLUMN_NAMES_ARRAY, true);
				if(columnNamesArray != null) {
					columnNamesList = Arrays.asList(columnNamesArray);
				} else {
					List<PropertySuffix> properties = getPropertiesUtil().getAllProperties(dataset, JRCsvQueryExecuterFactory.CSV_COLUMN_NAMES);
					if (properties != null && !properties.isEmpty()) {
						columnNamesList = new ArrayList<String>();
						for(int i = 0; i < properties.size(); i++) {
							String property = properties.get(i).getValue();
							columnNamesList.add(property);
						}
					} else {
						JRField[] fields = dataset.getFields();
						if (fields != null && fields.length > 0)
						{
							columnNamesList = new ArrayList<String>();
							for (int i = 0; i < fields.length; i++)
							{
								columnNamesList.add(fields[i].getName());
							}
						}
					}
				}
			}

			if (columnNamesList != null && columnNamesList.size() > 0) {
				List<String> splitColumnNamesList = new ArrayList<String>();
				for(int i = 0; i < columnNamesList.size(); i++) {
					String names = columnNamesList.get(i);
					for(String token: names.split(",")){
						splitColumnNamesList.add(token.trim());
					}
				}
				datasource.setColumnNames(splitColumnNamesList.toArray(new String[splitColumnNamesList.size()]));
			} else {
				if (log.isWarnEnabled()){
					log.warn("No column names were specified.");
				}
			}
			
			DateFormat dateFormat = (DateFormat) getParameterValue(JRCsvQueryExecuterFactory.CSV_DATE_FORMAT, true);
			if (dateFormat!=null) {
				datasource.setDateFormat(dateFormat);
			} else {
				String dateFormatPattern = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_DATE_PATTERN);
				if(dateFormatPattern != null){
					datasource.setDatePattern(dateFormatPattern);
				}
			}
			
			NumberFormat numberFormat = (NumberFormat) getParameterValue(JRCsvQueryExecuterFactory.CSV_NUMBER_FORMAT, true);
			if (numberFormat != null) {
				datasource.setNumberFormat(numberFormat);
			} else {
				String numberFormatPattern = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_NUMBER_PATTERN);
				if(numberFormatPattern != null){
					datasource.setNumberPattern(numberFormatPattern);
				}
			}

			String fieldDelimiter = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_FIELD_DELIMITER);
			if (fieldDelimiter != null && fieldDelimiter.length() > 0) {
				datasource.setFieldDelimiter(fieldDelimiter.charAt(0));
			}
			
			String recordDelimiter = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_RECORD_DELIMITER);
			if (recordDelimiter != null) {
				datasource.setRecordDelimiter(recordDelimiter);
			}
			
			datasource.setUseFirstRowAsHeader(getBooleanParameterOrProperty(JRCsvQueryExecuterFactory.CSV_USE_FIRST_ROW_AS_HEADER, false));
			
			Locale csvLocale = (Locale) getParameterValue(JRParameter.REPORT_LOCALE, true);
			if (csvLocale != null) {
				datasource.setLocale(csvLocale);
			} else {
				String csvLocaleCode = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_LOCALE_CODE);
				if (csvLocaleCode != null) {
					datasource.setLocale(csvLocaleCode);
				}
			}
			
			TimeZone csvTimezone = (TimeZone) getParameterValue(JRParameter.REPORT_TIME_ZONE, true);
			if (csvTimezone != null) {
				datasource.setTimeZone(csvTimezone);
			} else {
				String csvTimezoneId = getStringParameterOrProperty(JRCsvQueryExecuterFactory.CSV_TIMEZONE_ID);
				if (csvTimezoneId != null) {
					datasource.setTimeZone(csvTimezoneId);
				}
			}
		}
		
		return datasource;
	}

	public void close() {
		if(datasource != null){
			datasource.close();
		}
	}

	public boolean cancelQuery() throws JRException {
		return false;
	}

	@Override
	protected String getParameterReplacement(String parameterName) {
		return String.valueOf(getParameterValue(parameterName));
	}
	
}
