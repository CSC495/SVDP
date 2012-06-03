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
package net.sf.jasperreports.engine.fill;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRRewindableDataSource;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.data.IndexedDataSource;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: SortedDataSource.java 5221 2012-04-04 14:08:30Z lucianc $
 */
public class SortedDataSource implements JRRewindableDataSource, IndexedDataSource
{
	
	public static class SortRecord
	{
		private final Object[] values;
		private int recordIndex;
		private boolean filtered;
		
		public SortRecord(Object[] values, int recordIndex)
		{
			this.values = values;
			this.recordIndex = recordIndex;
			this.filtered = false;
		}
		
		protected void markFiltered()
		{
			filtered = true;
		}

		public Object fieldValue(int fieldIdx)
		{
			return values[fieldIdx];
		}
		
		public Object[] getValues()
		{
			return values;
		}

		protected void setRecordIndex(int recordIndex)
		{
			this.recordIndex = recordIndex;
		}

		public int getRecordIndex()
		{
			return recordIndex;
		}

		public boolean isFiltered()
		{
			return filtered;
		}
	}
	
	private final List<SortRecord> records;
	private final Integer[] recordIndexes;
	private final Map<String, Integer> columnNamesMap = new HashMap<String, Integer>();
	
	private int currentIndex;
	private SortRecord currentRecord;
	
	public SortedDataSource(List<SortRecord> records, Integer[] recordIndexes, String[] columnNames)
	{
		if (records.size() != recordIndexes.length)
		{
			throw new IllegalArgumentException("Record count " + records.size() 
					+ " doesn't match index count " + recordIndexes.length);
		}
		
		this.records = records;
		this.recordIndexes = recordIndexes;
		
		if (columnNames != null)
		{
			for(int i = 0; i < columnNames.length; i++)
			{
				columnNamesMap.put(columnNames[i], Integer.valueOf(i));
			}
		}

		this.currentIndex = 0;
	}

	public boolean next()
	{
		if (currentIndex >= recordIndexes.length)
		{
			return false;
		}
		
		int recordIndex = recordIndexes[currentIndex];
		// assuming random access
		currentRecord = records.get(recordIndex);
		++currentIndex;
		return true;
	}

	public void setRecordFilteredIndex(int index)
	{
		currentRecord.markFiltered();
		currentRecord.setRecordIndex(index);
	}

	public Object getFieldValue(JRField jrField)
	{
		Integer fieldIndex = columnNamesMap.get(jrField.getName());
		if (fieldIndex == null)
		{
			throw new JRRuntimeException("Field \"" + jrField.getName() + "\" not found in data source.");
		}
		return currentRecord.fieldValue(fieldIndex);
	}

	public void moveFirst()
	{
		currentIndex = 0;
	}

	@Override
	public int getRecordIndex()
	{
		return currentRecord.getRecordIndex();
	}

	public List<SortRecord> getRecords()
	{
		return records;
	}
}