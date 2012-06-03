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
package net.sf.jasperreports.engine.base;

import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.Serializable;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRSortField;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.type.SortFieldTypeEnum;
import net.sf.jasperreports.engine.type.SortOrderEnum;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRBaseSortField.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRBaseSortField implements JRSortField, Serializable, JRChangeEventsSupport
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	public static final String PROPERTY_ORDER = "order";

	/**
	 *
	 */
	protected String name;
	protected SortOrderEnum orderValue = SortOrderEnum.ASCENDING;
	protected SortFieldTypeEnum type = SortFieldTypeEnum.FIELD;


	/**
	 *
	 */
	protected JRBaseSortField()
	{
	}
	
	
	/**
	 *
	 */
	protected JRBaseSortField(JRSortField sortField, JRBaseObjectFactory factory)
	{
		factory.put(sortField, this);
		
		name = sortField.getName();
		orderValue = sortField.getOrderValue();
		type = sortField.getType();
	}
		

	/**
	 *
	 */
	public String getName()
	{
		return name;
	}
		
	/**
	 *
	 */
	public SortOrderEnum getOrderValue()
	{
		return orderValue;
	}
		
	/**
	 *
	 */
	public void setOrder(SortOrderEnum orderValue)
	{
		Object old = this.orderValue;
		this.orderValue = orderValue;
		getEventSupport().firePropertyChange(PROPERTY_ORDER, old, this.orderValue);
	}
	
	/**
	 *
	 */
	public SortFieldTypeEnum getType()
	{
		return type;
	}
		
	/**
	 * 
	 */
	public Object clone() 
	{
		try
		{
			JRBaseSortField clone = (JRBaseSortField)super.clone(); 
			clone.eventSupport = null;
			return clone;
		}
		catch (CloneNotSupportedException e)
		{
			throw new JRRuntimeException(e);
		}
	}
	
	private transient JRPropertyChangeSupport eventSupport;
	
	public JRPropertyChangeSupport getEventSupport()
	{
		synchronized (this)
		{
			if (eventSupport == null)
			{
				eventSupport = new JRPropertyChangeSupport(this);
			}
		}
		
		return eventSupport;
	}


	/*
	 * These fields are only for serialization backward compatibility.
	 */
	private int PSEUDO_SERIAL_VERSION_UID = JRConstants.PSEUDO_SERIAL_VERSION_UID; //NOPMD
	/**
	 * @deprecated
	 */
	private byte order;
	
	private void readObject(ObjectInputStream in) throws IOException, ClassNotFoundException
	{
		in.defaultReadObject();
		
		if (PSEUDO_SERIAL_VERSION_UID < JRConstants.PSEUDO_SERIAL_VERSION_UID_3_7_2)
		{
			orderValue = SortOrderEnum.getByValue((byte)(order + 1));
		}
		
		if (PSEUDO_SERIAL_VERSION_UID < JRConstants.PSEUDO_SERIAL_VERSION_UID_3_7_5)
		{
			type = SortFieldTypeEnum.FIELD;
		}
	}
	
}
