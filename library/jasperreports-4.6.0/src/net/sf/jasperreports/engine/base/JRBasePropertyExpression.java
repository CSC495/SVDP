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

import java.io.Serializable;

import net.sf.jasperreports.engine.JRCloneable;
import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRPropertyExpression;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.util.JRCloneUtils;

/**
 * Base implementation of {@link JRPropertyExpression}.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRBasePropertyExpression.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class JRBasePropertyExpression implements JRPropertyExpression, Serializable, JRChangeEventsSupport, JRCloneable
{
	
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	public static final String PROPERTY_NAME = "name";
	public static final String pROPERTY_VALUE_EXPRESSION = "valueExpression";

	private String name;
	private JRExpression valueExpression;

	protected JRBasePropertyExpression()
	{
		//empty
	}
	
	public JRBasePropertyExpression(JRPropertyExpression propertyExpression,
			JRBaseObjectFactory factory)
	{
		this.name = propertyExpression.getName();
		this.valueExpression = factory.getExpression(propertyExpression.getValueExpression());
	}

	public String getName()
	{
		return name;
	}

	public void setName(String name)
	{
		Object old = this.name;
		this.name = name;
		getEventSupport().firePropertyChange(PROPERTY_NAME, old, this.name);
	}

	public JRExpression getValueExpression()
	{
		return valueExpression;
	}

	protected void setValueExpression(JRExpression valueExpression)
	{
		Object old = this.valueExpression;
		this.valueExpression = valueExpression;
		getEventSupport().firePropertyChange(pROPERTY_VALUE_EXPRESSION, old, this.valueExpression);
	}
	
	/**
	 * 
	 */
	public Object clone()
	{
		JRBasePropertyExpression clone = null;
		
		try
		{
			clone = (JRBasePropertyExpression) super.clone();
		}
		catch (CloneNotSupportedException e)
		{
			throw new JRRuntimeException(e);
		}
		
		clone.valueExpression = JRCloneUtils.nullSafeClone(valueExpression);
		clone.eventSupport = null;
		
		return clone;
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

}
