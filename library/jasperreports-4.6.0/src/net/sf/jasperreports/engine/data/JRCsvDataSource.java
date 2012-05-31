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
package net.sf.jasperreports.engine.data;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.Reader;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.text.DateFormat;
import java.text.NumberFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.util.FormatUtils;
import net.sf.jasperreports.repo.RepositoryUtil;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;


/**
 * This datasource implementation reads a CSV stream. Datasource rows are separated by a record delimiter string and
 * fields inside a row are separated by a field delimiter character. Fields containing delimiter characters can be
 * placed inside quotes. If fields contain quotes themselves, these are duplicated (example: <i>"John ""Doe"""<i> will be
 * displayed as <i>John "Doe"</i>).
 * <p>
 * Since CSV does not specify column names, the default naming convention is to name report fields COLUMN_x and map each
 * column with the field found at index x in each row (these indices start with 0). To avoid this situation, users can
 * either specify a collection of column names or set a flag to read the column names from the first row of the CSV file.
 *
 * @author Ionut Nedelcu (ionutned@users.sourceforge.net)
 * @version $Id: JRCsvDataSource.java 5346 2012-05-08 12:08:01Z teodord $
 */
public class JRCsvDataSource extends JRAbstractTextDataSource// implements JRDataSource
{
	protected static final Log log = LogFactory.getLog(JRCsvDataSource.class);
	
	private DateFormat dateFormat;
	private NumberFormat numberFormat;
	private char fieldDelimiter = ',';
	private String recordDelimiter = "\n";
	private Map<String, Integer> columnNames = new LinkedHashMap<String, Integer>();
	private boolean useFirstRowAsHeader;

	private List<String> fields;
	private Reader reader;
	private char buffer[] = new char[1024];
	private int position;
	private int bufSize;
	private boolean processingStarted;
	private boolean toClose;

	//TODO: parametrize this value
	private boolean isStrictCsv = true;

	/**
	 * Creates a datasource instance from a CSV data input stream, using the default encoding.
	 * @param stream an input stream containing CSV data
	 */
	public JRCsvDataSource(InputStream stream)
	{
		this(new BufferedReader(new InputStreamReader(stream)));
	}


	/**
	 * Creates a datasource instance from a CSV data input stream, using the specified encoding.
	 * @param stream an input stream containing CSV data
	 * @param charsetName the encoding to use
	 */
	public JRCsvDataSource(InputStream stream, String charsetName) throws UnsupportedEncodingException
	{
		this(new BufferedReader(new InputStreamReader(stream, charsetName)));
	}


	/**
	 * Creates a datasource instance that reads CSV data from a given URL, using the default encoding.
	 * @param url an URL from where to read CSV data
	 */
	public JRCsvDataSource(URL url) throws IOException
	{
		this(url.openStream());
		
		toClose = true;
	}


	/**
	 * Creates a datasource instance that reads CSV data from a given URL, using the specified encoding.
	 * @param url an URL from where to read CSV data
	 */
	public JRCsvDataSource(URL url, String charsetName) throws IOException
	{
		this(url.openStream(), charsetName);
		
		toClose = true;
	}


	/**
	 * Creates a datasource instance from a CSV file, using the default encoding.
	 * @param file a file containing CSV data
	 */
	public JRCsvDataSource(File file) throws FileNotFoundException
	{
		this(new FileInputStream(file));
		
		toClose = true;
	}


	/**
	 * Creates a datasource instance from a CSV file, using the specified encoding.
	 * @param file a file containing CSV data
	 * @param charsetName the encoding to use
	 */
	public JRCsvDataSource(File file, String charsetName) throws FileNotFoundException, UnsupportedEncodingException
	{
		this(new FileInputStream(file), charsetName);
		
		toClose = true;
	}


	/**
	 * Creates a datasource instance that reads CSV data from a given location, using the default encoding.
	 * @param jasperReportsContext the JasperReportsContext
	 * @param location a String representing CSV data source
	 */
	public JRCsvDataSource(JasperReportsContext jasperReportsContext, String location) throws JRException
	{
		this(RepositoryUtil.getInstance(jasperReportsContext).getInputStreamFromLocation(location));
		
		toClose = true;
	}


	/**
	 * Creates a datasource instance that reads CSV data from a given location, using the specified encoding.
	 * @param jasperReportsContext the JasperReportsContext
	 * @param location a String representing CSV data source
	 * @param charsetName the encoding to use
	 */
	public JRCsvDataSource(JasperReportsContext jasperReportsContext, String location, String charsetName) throws JRException, UnsupportedEncodingException
	{
		this(RepositoryUtil.getInstance(jasperReportsContext).getInputStreamFromLocation(location), charsetName);
		
		toClose = true;
	}


	/**
	 * @see #JRCsvDataSource(JasperReportsContext, String)
	 */
	public JRCsvDataSource(String location) throws JRException
	{
		this(DefaultJasperReportsContext.getInstance(), location);
	}


	/**
	 * @see #JRCsvDataSource(JasperReportsContext, String, String)
	 */
	public JRCsvDataSource(String location, String charsetName) throws JRException, UnsupportedEncodingException
	{
		this(DefaultJasperReportsContext.getInstance(), location, charsetName);
	}


	/**
	 * Creates a datasource instance from a CSV data reader.
	 * @param reader a <tt>Reader</tt> instance, for reading the stream
	 */
	public JRCsvDataSource(Reader reader)
	{
		this.reader = reader;
	}


	/**
	 *
	 */
	public boolean next() throws JRException
	{
		try {
			if (!processingStarted) {
				if (useFirstRowAsHeader) 
				{
					parseRow();
					this.columnNames = new LinkedHashMap<String, Integer>();
					for (int i = 0; i < fields.size(); i++) {
						String name = fields.get(i);
						this.columnNames.put(name, Integer.valueOf(i));
					}
				}
				processingStarted = true;
			}

			return parseRow();
		} catch (IOException e) {
			throw new JRException(e);
		}
	}

	/**
	 *
	 */
	public Object getFieldValue(JRField jrField) throws JRException
	{
		String fieldName = jrField.getName();

		Integer columnIndex = columnNames.get(fieldName);
		if (columnIndex == null && fieldName.startsWith("COLUMN_"))
		{
			columnIndex = Integer.valueOf(fieldName.substring(7));
		}
		if (columnIndex == null)
		{
			throw new JRException("Unknown column name : " + fieldName);
		}

		if (fields.size() > columnIndex.intValue()) 
		{
			String fieldValue = fields.get(columnIndex.intValue());
			Class<?> valueClass = jrField.getValueClass();
			
			if (valueClass.equals(String.class))
			{
				return fieldValue;
			}

			fieldValue = fieldValue.trim();
			
			if (fieldValue.length() == 0)
			{
				return null;
			}
			
			try {
				if (valueClass.equals(Boolean.class)) 
				{
					return fieldValue.equalsIgnoreCase("true") ? Boolean.TRUE : Boolean.FALSE;
				}
				else if (Number.class.isAssignableFrom(valueClass))
				{
					if (numberFormat != null)
					{
						return FormatUtils.getFormattedNumber(numberFormat, fieldValue, valueClass);
					}
					else 
					{
						return convertStringValue(fieldValue, valueClass);
					}
				}
				else if (Date.class.isAssignableFrom(valueClass)){
					if (dateFormat != null)
					{
						return FormatUtils.getFormattedDate(dateFormat, fieldValue, valueClass);
					} 
					else
					{
						return convertStringValue(fieldValue, valueClass);
					}
				}
				else
				{
					throw new JRException("Field '" + jrField.getName() + "' is of class '" + valueClass.getName() + "' and can not be converted");
				}
			} catch (Exception e) {
				throw new JRException("Unable to get value for field '" + jrField.getName() + "' of class '" + valueClass.getName() + "'", e);
			}
		}

		return null;
	}


	/**
	 * Parses a row of CSV data and extracts the fields it contains
	 */
	private boolean parseRow() throws IOException, JRException
	{
		int pos = 0;
		int startFieldPos = 0;
		int addedFields = 0;
		boolean insideQuotes = false;
		boolean isQuoted = false;
		boolean misplacedQuote = false;
		boolean startPosition = false;
		char c;
		int leadingSpaces = 0;
		fields = new ArrayList<String>();

		String row = getRow();
		if (row == null)// || row.length() == 0)
		{
			return false;
		}
		
		//removing the unicode BOM which occurs only once, at the beginning of the file
		if(row.length() > 0 && row.charAt(0) == '\ufeff')
		{
			row = row.substring(1);
		}

		while (pos < row.length()) {
			c = row.charAt(pos);
			
			if(pos == startFieldPos)
			{
				//determining the number of white spaces at the beginning of a field
				//this is necessary in order to determine if a trimmed field is quoted
				while( pos + leadingSpaces < row.length() 
						&& row.charAt(pos + leadingSpaces) <= ' '  // this is how trim() works in java sun jdk 1.5; only chars <= ' ' are trimmed
						&& row.charAt(pos + leadingSpaces) != fieldDelimiter
						)
					{
						++leadingSpaces;
					}
			}
			if (c == '"') {
				startPosition = pos == startFieldPos + leadingSpaces || (!insideQuotes && row.charAt(pos-1) == fieldDelimiter);

				if (startPosition) 
				{
					// starting a quoted text
					insideQuotes = true;
					isQuoted = true;
				}
				else {
					if (insideQuotes )
					{
						if(pos+1 < row.length())
						{
							// when already inside quotes, expecting two consecutive quotes, 
							// otherwise it should be a closing quote

							if(row.charAt(pos+1) == '"')
							{
								pos++;
							}
							else
							{
								//testing if white spaces follow after the closing quote; 
								int trailingSpaces = 1;
								while(pos + trailingSpaces < row.length() && row.charAt(pos + trailingSpaces)<= ' ')
								{
									++trailingSpaces;
								}
								
								//TODO: handling misplaced quotes along with parametrized isStrictCsv; 
								if(pos + trailingSpaces < row.length() && row.charAt(pos + trailingSpaces) != fieldDelimiter)
								{
									misplacedQuote = true;
									if(isStrictCsv)
									{
										throw new JRException("Misplaced quote found at position " + pos + " in row: " + row);
									}
								}
								insideQuotes = false;
							}
						}
						else
						{
							insideQuotes = false;
						}
					}
					else
					{
						if(isStrictCsv)
						{
							throw new JRException("Misplaced quote found at position " + pos + " in row: " + row);
						}
					}
				}
			}
			
			// field delimiter found, copy the field contents to the field array
			if (c == fieldDelimiter && !insideQuotes) 
			{
				String field = row.substring(startFieldPos, pos);
				field = field.trim();
				
				if (isQuoted) 
				{
					if (field.endsWith("\"")) 
					{
						field = field.substring(0, field.length() - 1);
					}
					else if(isStrictCsv)
					{
						throw new JRException("Malformed quoted field: " + field);
					}
					field = field.substring(1);
					field = replaceAll(field, "\"\"", "\"");
				}
					
				// if an illegal quote was found, the occurrence will be logged
				if (misplacedQuote) 
				{
					//TODO: handle misplaced quotes along with parametrized isStrictCsv; 
					//if !isStrictCsv the misplaced quote is allowed to be printed as part of quoted field, 
					//although it is not doubled; the presence of a misplaced quote inside a field is
					//logged at logger debug level
					misplacedQuote = false;
					if (log.isDebugEnabled())
					{
						log.debug("Undoubled quote found in quoted field: " + field);
					}
				}

				isQuoted = false;
				insideQuotes = false;
				fields.add(field);
				++addedFields;
				
				// if many rows were concatenated due to misplacing of starting and ending quotes in a multiline field 
				// is possible to get more fields in the resulting row than the number of columns
				if(addedFields == columnNames.size())
				{
					addedFields = 0;
				}
				startFieldPos = pos + 1;
				leadingSpaces = 0;
			}

			pos++;
			
			// if the record delimiter was found inside a quoted field, it is not an actual record delimiter,
			// so another line should be read
			if ((pos == row.length()) && insideQuotes) 
			{
				String newRow = getRow();
				if(newRow != null)
				{
					row = row + recordDelimiter + newRow;
				}
			}
		}

		// end of row was reached, so the final characters form the last field in the record
		String field = row.substring(startFieldPos, pos);
		if (field == null)
		{
			return true;
		}

		if (misplacedQuote)
		{
			//TODO: handle misplaced quotes along with parametrized isStrictCsv; 
			//if !isStrictCsv the misplaced quote is allowed to be printed as part of quoted field, 
			//although it is not doubled; the presence of a misplaced quote inside a field is
			//logged at logger debug level
			if(isStrictCsv)
			{
				throw new JRException("Misplaced quote found in field: " + field);
			}

			if (log.isDebugEnabled())
			{
				log.debug("Undoubled quote found in field: " + field);
			}
		}
		
		field = field.trim();
		if (isQuoted) 
		{
			if (field.endsWith("\"")) 
			{
				field = field.substring(0, field.length() - 1);
			}
			else if(isStrictCsv)
			{
				throw new JRException("Malformed quoted field: " + field);
			}
			field = field.substring(1);
			field = replaceAll(field, "\"\"", "\"");
		}
		
		fields.add(field);
		++addedFields;
		while(addedFields < columnNames.size())
		{
			fields.add("");
			++addedFields;
		}
		
		return true;
	}


	/**
	 * Reads a row from the stream. A row is a sequence of characters separated by the record delimiter.
	 */
	private String getRow() throws IOException
	{
		StringBuffer row = new StringBuffer();
		char c;

		while (true) 
		{
			try 
			{
				c = getChar();

				// searches for the first character of the record delimiter
				if (c == recordDelimiter.charAt(0) || c == Character.MIN_VALUE) {
					int i;
					char[] temp = new char[recordDelimiter.length()];
					temp[0] = c;
					boolean isDelimiter = true;
					// checks if the following characters in the stream form the record delimiter
					for (i = 1; i < recordDelimiter.length() && isDelimiter; i++) {
						temp[i] = getChar();
						if (temp[i] != recordDelimiter.charAt(i))
						{
							isDelimiter = false;
						}
					}

					if (isDelimiter)
					{
						return row.toString();
					}
					row.append(temp, 0, i);
				}

				row.append(c);
			}
			catch (JRException e) 
			{
				if (row.length() == 0)
				{
					return null;
				}
				else
				{
					return row.toString();
				}
			}

		} // end while
	}

	/**
	 * Reads a character from the stream.
	 * @throws IOException if any I/O error occurs
	 * @throws JRException if end of stream has been reached
	 */
	private char getChar() throws IOException, JRException
	{
		// end of buffer, fill a new buffer
		if (position + 1 > bufSize) {
			bufSize = reader.read(buffer);
			position = 0;
			if (bufSize == -1)
			{
				throw new JRException("No more chars");
			}
		}

		return buffer[position++];
	}


	/**
	 * Gets the date format that will be used to parse date fields
	 */
	public DateFormat getDateFormat()
	{
		return dateFormat;
	}


	/**
	 * Sets the desired date format to be used for parsing date fields
	 */
	public void setDateFormat(DateFormat dateFormat)
	{
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.dateFormat = dateFormat;
	}


	/**
	 * Returns the field delimiter character.
	 */
	public char getFieldDelimiter()
	{
		return fieldDelimiter;
	}


	/**
	 * Sets the field delimiter character. The default is comma. If characters such as comma or quotes are specified,
	 * the results can be unpredictable.
	 * @param fieldDelimiter
	 */
	public void setFieldDelimiter(char fieldDelimiter)
	{
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.fieldDelimiter = fieldDelimiter;
	}


	/**
	 * Returns the record delimiter string.
	 */
	public String getRecordDelimiter()
	{
		return recordDelimiter;
	}


	/**
	 * Sets the record delimiter string. The default is line feed (\n).
	 * @param recordDelimiter
	 */
	public void setRecordDelimiter(String recordDelimiter)
	{
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.recordDelimiter = recordDelimiter;
	}


	/**
	 * Specifies an array of strings representing column names matching field names in the report template
	 */
	public void setColumnNames(String[] columnNames)
	{
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.columnNames = new LinkedHashMap<String, Integer>();
		for (int i = 0; i < columnNames.length; i++)
		{
			this.columnNames.put(columnNames[i], Integer.valueOf(i));
		}
	}


	/**
	 * Specifies whether the first line of the CSV file should be considered a table
	 * header, containing column names matching field names in the report template
	 */
	public void setUseFirstRowAsHeader(boolean useFirstRowAsHeader)
	{
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.useFirstRowAsHeader = useFirstRowAsHeader;
	}


	/**
	 * Closes the reader. Users of this data source should close it after usage.
	 */
	public void close()
	{
		if (toClose)
		{
			try
			{
				reader.close();
			}
			catch(IOException e)
			{
				//nothing to do
			}
		}
	}


	private String replaceAll(String string, String substring, String replacement)
	{
		StringBuffer result = new StringBuffer();
		int index = string.indexOf(substring);
		int oldIndex = 0;
		while (index >= 0) {
			result.append(string.substring(oldIndex, index));
			result.append(replacement);
			index += substring.length();
			oldIndex = index;

			index = string.indexOf(substring, index);
		}

		if (oldIndex <  string.length())
		{
			result.append(string.substring(oldIndex, string.length()));
		}

		return result.toString();
	}


	public NumberFormat getNumberFormat() {
		return numberFormat;
	}


	public void setNumberFormat(NumberFormat numberFormat) {
		if (processingStarted)
		{
			throw new JRRuntimeException("Cannot modify data source properties after data reading has started");
		}
		this.numberFormat = numberFormat;
	}
	
	public Map<String, Integer> getColumnNames() {
		return columnNames;
	}
	
	
}
