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
 * Artur Biesiadowski - abies@users.sourceforge.net 
 */
package net.sf.jasperreports.engine.data;

import java.util.HashMap;

import javax.swing.table.TableModel;

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRRewindableDataSource;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRTableModelDataSource.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRTableModelDataSource implements JRRewindableDataSource
{
	

	/**
	 *
	 */
	private TableModel tableModel;
	private int index = -1;
	private HashMap<String, Integer> columnNames = new HashMap<String, Integer>();
	

	/**
	 *
	 */
	public JRTableModelDataSource(TableModel model)
	{
		this.tableModel = model;
		
		if (this.tableModel != null)
		{
			for(int i = 0; i < tableModel.getColumnCount(); i++)
			{
				this.columnNames.put(tableModel.getColumnName(i), Integer.valueOf(i));
			}
		}
	}
	

	/**
	 *
	 */
	public boolean next()
	{
		this.index++;

		if (this.tableModel != null)
		{
			return (this.index < this.tableModel.getRowCount());
		}

		return false;
	}
	
	
	/**
	 *
	 */
	public Object getFieldValue(JRField jrField) throws JRException
	{
		String fieldName = jrField.getName();
		
		Integer columnIndex = this.columnNames.get(fieldName);
		
		if (columnIndex != null)
		{
			return this.tableModel.getValueAt(index, columnIndex.intValue());
		}
		else if (fieldName.startsWith("COLUMN_"))
		{
			return this.tableModel.getValueAt(index, Integer.parseInt(fieldName.substring(7)));
		}
		else
		{
			throw new JRException("Unknown column name : " + fieldName);
		}
	}

	
	/**
	 *
	 */
	public void moveFirst()
	{
		this.index = -1;
	}


}
