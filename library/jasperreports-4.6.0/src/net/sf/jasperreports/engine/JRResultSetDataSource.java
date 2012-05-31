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
 * S. Brett Sutton - bsutton@idatam.com.au
 */
package net.sf.jasperreports.engine;

import java.awt.Image;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.CharArrayReader;
import java.io.CharArrayWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.Reader;
import java.sql.Blob;
import java.sql.Clob;
import java.sql.ResultSet;
import java.sql.ResultSetMetaData;
import java.sql.SQLException;
import java.sql.Types;
import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;
import java.util.TimeZone;

import net.sf.jasperreports.engine.query.JRJdbcQueryExecuterFactory;
import net.sf.jasperreports.engine.util.JRImageLoader;


/**
 * An implementation of a data source that uses a supplied <tt>ResultSet</tt>.
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRResultSetDataSource.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRResultSetDataSource implements JRDataSource
{


	private static final String INDEXED_COLUMN_PREFIX = "COLUMN_";
	private static final int INDEXED_COLUMN_PREFIX_LENGTH = INDEXED_COLUMN_PREFIX.length();
	
	/**
	 *
	 */
	private JasperReportsContext jasperReportsContext;
	private ResultSet resultSet;
	private Map<String,Integer> columnIndexMap = new HashMap<String,Integer>();

	private TimeZone timeZone;
	private boolean timeZoneOverride;
	private Map<JRField, Calendar> fieldCalendars = new HashMap<JRField, Calendar>();


	/**
	 *
	 */
	public JRResultSetDataSource(JasperReportsContext jasperReportsContext, ResultSet resultSet)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.resultSet = resultSet;
	}


	/**
	 * @see #JRResultSetDataSource(JasperReportsContext, ResultSet)
	 */
	public JRResultSetDataSource(ResultSet resultSet)
	{
		this(DefaultJasperReportsContext.getInstance(), resultSet);
	}


	/**
	 *
	 */
	public boolean next() throws JRException
	{
		boolean hasNext = false;
		
		if (resultSet != null)
		{
			try
			{
				hasNext = resultSet.next();
			}
			catch (SQLException e)
			{
				throw new JRException("Unable to get next record.", e);
			}
		}
		
		return hasNext;
	}


	/**
	 *
	 */
	public Object getFieldValue(JRField field) throws JRException
	{
		Object objValue = null;

		if (field != null && resultSet != null)
		{
			Integer columnIndex = getColumnIndex(field.getName());
			Class<?> clazz = field.getValueClass();

			try
			{
				if (clazz.equals(java.lang.Boolean.class))
				{
					objValue = resultSet.getBoolean(columnIndex.intValue()) ? Boolean.TRUE : Boolean.FALSE;
				}
				else if (clazz.equals(java.lang.Byte.class))
				{
					objValue = new Byte(resultSet.getByte(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (
					clazz.equals(java.util.Date.class)
					|| clazz.equals(java.sql.Date.class)
					)
				{
					objValue = readDate(columnIndex, field);
				}
				else if (clazz.equals(java.sql.Timestamp.class))
				{
					objValue = readTimestamp(columnIndex, field);
				}
				else if (clazz.equals(java.sql.Time.class))
				{
					objValue = readTime(columnIndex, field);
				}
				else if (clazz.equals(java.lang.Double.class))
				{
					objValue = new Double(resultSet.getDouble(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.lang.Float.class))
				{
					objValue = new Float(resultSet.getFloat(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.lang.Integer.class))
				{
					objValue = Integer.valueOf(resultSet.getInt(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.io.InputStream.class))
				{
					byte[] bytes = readBytes(columnIndex);
					
					if(bytes == null)
					{
						objValue = null;
					}
					else
					{
						objValue = new ByteArrayInputStream(bytes);
					}					
				}
				else if (clazz.equals(java.lang.Long.class))
				{
					objValue = new Long(resultSet.getLong(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.lang.Short.class))
				{
					objValue = new Short(resultSet.getShort(columnIndex.intValue()));
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.math.BigDecimal.class))
				{
					objValue = resultSet.getBigDecimal(columnIndex.intValue());
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(java.lang.String.class))
				{
					int columnType = resultSet.getMetaData().getColumnType(columnIndex.intValue());
					switch (columnType)
					{
						case Types.CLOB:
							Clob clob = resultSet.getClob(columnIndex.intValue());
							if (resultSet.wasNull())
							{
								objValue = null;
							}
							else
							{
								objValue = clobToString(clob);
							}
							break;
							
						default:
							objValue = resultSet.getString(columnIndex.intValue());
							if(resultSet.wasNull())
							{
								objValue = null;
							}
							break;
					}
				}
				else if (clazz.equals(Clob.class))
				{
					objValue = resultSet.getClob(columnIndex.intValue());
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(Reader.class))
				{
					Reader reader = null;
					long size = -1;
					
					int columnType = resultSet.getMetaData().getColumnType(columnIndex.intValue());
					switch (columnType)
					{
						case Types.CLOB:
							Clob clob = resultSet.getClob(columnIndex.intValue());
							if (!resultSet.wasNull())
							{
								reader = clob.getCharacterStream();
								size = clob.length();
							}
							break;
							
						default:
							reader = resultSet.getCharacterStream(columnIndex.intValue());
							if (resultSet.wasNull())
							{
								reader = null; 
							}
					}
					
					if (reader == null)
					{
						objValue = null;
					}
					else
					{
						objValue = getArrayReader(reader, size);
					}
				}
				else if (clazz.equals(Blob.class))
				{
					objValue = resultSet.getBlob(columnIndex.intValue());
					if(resultSet.wasNull())
					{
						objValue = null;
					}
				}
				else if (clazz.equals(Image.class))
				{
					byte[] bytes = readBytes(columnIndex);
					
					if(bytes == null)
					{
						objValue = null;
					}
					else
					{
						objValue = JRImageLoader.getInstance(jasperReportsContext).loadAwtImageFromBytes(bytes);
					}					
				}
				else
				{
					objValue = resultSet.getObject(columnIndex.intValue());
				}
			}
			catch (Exception e)
			{
				throw new JRException("Unable to get value for field '" + field.getName() + "' of class '" + clazz.getName() + "'", e);
			}
		}
		
		return objValue;
	}


	protected Object readDate(Integer columnIndex, JRField field) throws SQLException
	{
		Calendar calendar = getFieldCalendar(field);
		Object objValue = calendar == null ? resultSet.getDate(columnIndex.intValue())
				: resultSet.getDate(columnIndex.intValue(), calendar);
		if(resultSet.wasNull())
		{
			objValue = null;
		}
		return objValue;
	}


	protected Object readTimestamp(Integer columnIndex, JRField field) throws SQLException
	{
		Calendar calendar = getFieldCalendar(field);
		Object objValue = calendar == null ? resultSet.getTimestamp(columnIndex.intValue())
				: resultSet.getTimestamp(columnIndex.intValue(), calendar);
		if(resultSet.wasNull())
		{
			objValue = null;
		}
		return objValue;
	}


	protected Object readTime(Integer columnIndex, JRField field) throws SQLException
	{
		Calendar calendar = getFieldCalendar(field);
		Object objValue = calendar == null ? resultSet.getTime(columnIndex.intValue())
				: resultSet.getTime(columnIndex.intValue(), calendar);
		if(resultSet.wasNull())
		{
			objValue = null;
		}
		return objValue;
	}





	/**
	 *
	 */
	private Integer getColumnIndex(String fieldName) throws JRException
	{
		Integer columnIndex = columnIndexMap.get(fieldName);
		if (columnIndex == null)
		{
			try
			{
				columnIndex = searchColumnByName(fieldName);
				
				if (columnIndex == null)
				{
					columnIndex = searchColumnByLabel(fieldName);
				}
				
				if (columnIndex == null && fieldName.startsWith(INDEXED_COLUMN_PREFIX))
				{
					columnIndex = Integer.valueOf(fieldName.substring(INDEXED_COLUMN_PREFIX_LENGTH));
					if (
						columnIndex.intValue() <= 0
						|| columnIndex.intValue() > resultSet.getMetaData().getColumnCount()
						)
					{
						throw new JRException("Column index out of range : " + columnIndex);
					}
				}
				
				if (columnIndex == null)
				{
					throw new JRException("Unknown column name : " + fieldName);
				}
			}
			catch (SQLException e)
			{
				throw new JRException("Unable to retrieve result set metadata.", e);
			}

			columnIndexMap.put(fieldName, columnIndex);
		}
		
		return columnIndex;
	}


	protected Integer searchColumnByName(String fieldName) throws SQLException
	{
		Integer columnIndex = null;
		ResultSetMetaData metadata = resultSet.getMetaData();
		for(int i = 1; i <= metadata.getColumnCount(); i++)
		{
			String columnName = metadata.getColumnName(i);
			if (fieldName.equalsIgnoreCase(columnName))
			{
				columnIndex = Integer.valueOf(i);
				break;
			}
		}
		return columnIndex;
	}


	protected Integer searchColumnByLabel(String fieldName) throws SQLException
	{
		Integer columnIndex = null;
		ResultSetMetaData metadata = resultSet.getMetaData();
		for(int i = 1; i <= metadata.getColumnCount(); i++)
		{
			String columnLabel = metadata.getColumnLabel(i);
			if (columnLabel != null && fieldName.equalsIgnoreCase(columnLabel))
			{
				columnIndex = Integer.valueOf(i);
				break;
			}
		}
		return columnIndex;
	}


	protected String clobToString(Clob clob) throws JRException
	{
		try
		{
			int bufSize = 8192;
			char[] buf = new char[bufSize];
			
			Reader reader = new BufferedReader(clob.getCharacterStream(), bufSize);
			StringBuffer str = new StringBuffer((int) clob.length());
			
			for (int read = reader.read(buf); read > 0; read = reader.read(buf))
			{
				str.append(buf, 0, read);
			}

			return str.toString();
		}
		catch (SQLException e)
		{
			throw new JRException("Unable to read clob value", e);
		}
		catch (IOException e)
		{
			throw new JRException("Unable to read clob value", e);
		}
	}

	protected CharArrayReader getArrayReader(Reader reader, long size) throws IOException
	{
		char[] buf = new char[8192];
		CharArrayWriter bufWriter = new CharArrayWriter((size > 0) ? (int) size : 8192);
		
		BufferedReader bufReader = new BufferedReader(reader, 8192);
		for (int read = bufReader.read(buf); read > 0; read = bufReader.read(buf))
		{
			bufWriter.write(buf, 0, read);
		}
		bufWriter.flush();
		
		return new CharArrayReader(bufWriter.toCharArray());
	}

	protected byte[] readBytes(Integer columnIndex) throws SQLException, IOException
	{
		InputStream is = null;
		long size = -1;
		
		int columnType = resultSet.getMetaData().getColumnType(columnIndex.intValue());
		switch (columnType)
		{
			case Types.BLOB:
				Blob blob = resultSet.getBlob(columnIndex.intValue());
				if (!resultSet.wasNull())
				{
					is = blob.getBinaryStream();
					size = blob.length();
				}
				break;
				
			default:
				is = resultSet.getBinaryStream(columnIndex.intValue());
				if (resultSet.wasNull())
				{
					is = null; 
				}
		}
		
		byte[] bytes = null;
		if (is != null)
		{
			bytes = readBytes(is, size);
		}
		
		return bytes;
	}

	protected byte[] readBytes(InputStream is, long size) throws IOException
	{
		ByteArrayOutputStream baos = new ByteArrayOutputStream(size > 0 ? (int) size : 1000);
		byte[] bytes = new byte[1000];
		int ln = 0;
		try
		{
			while ((ln = is.read(bytes)) > 0)
			{
				baos.write(bytes, 0, ln);
			}
			baos.flush();
		}
		finally
		{
			try
			{
				baos.close();
			}
			catch(IOException e)
			{
			}
		}
		return baos.toByteArray();
	}

	/**
	 * Sets the default time zone to be used for retrieving date/time values from the 
	 * result set.
	 * 
	 * In most cases no explicit time zone conversion would be required for retrieving 
	 * date/time values from the DB, and this parameter should be null.  
	 * 
	 * @param timeZone the default time zone
	 * @param override whether the default time zone overrides time zones specified
	 * as field-level properties
	 * @see JRJdbcQueryExecuterFactory#PROPERTY_TIME_ZONE
	 */
	public void setTimeZone(TimeZone timeZone, boolean override)
	{
		this.timeZone = timeZone;
		this.timeZoneOverride = override;
	}
	
	protected Calendar getFieldCalendar(JRField field)
	{
		if (fieldCalendars.containsKey(field))
		{
			return fieldCalendars.get(field);
		}
		
		Calendar calendar = createFieldCalendar(field);
		fieldCalendars.put(field, calendar);
		return calendar;
	}

	protected Calendar createFieldCalendar(JRField field)
	{
		TimeZone tz;
		if (timeZoneOverride)
		{
			// if we have a parameter, use it
			tz = timeZone;
		}
		else
		{
			if (field.hasProperties() && field.getPropertiesMap().containsProperty(
					JRJdbcQueryExecuterFactory.PROPERTY_TIME_ZONE))
			{
				// read the field level property
				String timezoneId = JRPropertiesUtil.getInstance(jasperReportsContext).getProperty(field, 
						JRJdbcQueryExecuterFactory.PROPERTY_TIME_ZONE);
				tz = (timezoneId == null || timezoneId.length() == 0) ? null 
						: TimeZone.getTimeZone(timezoneId);
			}
			else
			{
				// dataset/default property
				tz = timeZone;
			}
		}

		// using default JVM locale for the calendar
		Calendar cal = tz == null ? null : Calendar.getInstance(tz);
		return cal;
	}
}
