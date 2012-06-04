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

import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRField;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRBeanArrayDataSource.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRBeanArrayDataSource extends JRAbstractBeanDataSource
{
	

	/**
	 *
	 */
	private Object[] data;
	private int index = -1;
	

	/**
	 *
	 */
	public JRBeanArrayDataSource(Object[] beanArray)
	{
		this(beanArray, true);
	}
	

	/**
	 *
	 */
	public JRBeanArrayDataSource(Object[] beanArray, boolean isUseFieldDescription)
	{
		super(isUseFieldDescription);
		
		this.data = beanArray;
	}
	

	/**
	 *
	 */
	public boolean next()
	{
		this.index++;

		if (this.data != null)
		{
			return (this.index < this.data.length);
		}

		return false;
	}
	
	
	/**
	 *
	 */
	public Object getFieldValue(JRField field) throws JRException
	{
		return getFieldValue(data[this.index], field);
	}

	
	/**
	 *
	 */
	public void moveFirst()
	{
		this.index = -1;
	}

	/**
	 * Returns the underlying bean array used by this data source.
	 * 
	 * @return the underlying bean array
	 */
	public Object[] getData()
	{
		return data;
	}

	/**
	 * Returns the total number of records/beans that this data source
	 * contains.
	 * 
	 * @return the total number of records of this data source
	 */
	public int getRecordCount()
	{
		return data == null ? 0 : data.length;
	}
	
	/**
	 * Clones this data source by creating a new instance that reuses the same
	 * underlying bean array. 
	 * 
	 * @return a clone of this data source
	 */
	public JRBeanArrayDataSource cloneDataSource()
	{
		return new JRBeanArrayDataSource(data);
	}

}
