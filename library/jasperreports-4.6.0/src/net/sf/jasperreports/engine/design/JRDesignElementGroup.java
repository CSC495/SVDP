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
package net.sf.jasperreports.engine.design;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRElementGroup;
import net.sf.jasperreports.engine.base.JRBaseElementGroup;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRDesignElementGroup.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRDesignElementGroup extends JRBaseElementGroup implements JRChangeEventsSupport
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;


	public static final String PROPERTY_ELEMENT_GROUP = "elementGroup";

	public static final String PROPERTY_CHILDREN = "children";

	/**
	 *
	 */
	public void setElementGroup(JRElementGroup elementGroup)
	{
		Object old = this.elementGroup;
		this.elementGroup = elementGroup;
		getEventSupport().firePropertyChange(PROPERTY_ELEMENT_GROUP, old, this.elementGroup);
	}

	/**
	 *
	 */
	public void addElement(JRDesignElement element)
	{
		addElement(children.size(), element);
	}

	/**
	 *
	 */
	public void addElement(int index, JRDesignElement element)
	{
		element.setElementGroup(this);
		
		this.children.add(index, element);
		getEventSupport().fireCollectionElementAddedEvent(PROPERTY_CHILDREN, element, index);
	}

	/**
	 *
	 */
	public JRDesignElement removeElement(JRDesignElement element)
	{
		element.setElementGroup(null);

		int idx = this.children.indexOf(element);
		if (idx >= 0)
		{
			this.children.remove(idx);
			getEventSupport().fireCollectionElementRemovedEvent(PROPERTY_CHILDREN, element, idx);
		}
		
		return element;
	}

	/**
	 *
	 */
	public void addElementGroup(JRDesignElementGroup elemGrp)
	{
		addElementGroup(children.size(), elemGrp);
	}

	/**
	 *
	 */
	public void addElementGroup(int index, JRDesignElementGroup elemGrp)
	{
		elemGrp.setElementGroup(this);
		
		this.children.add(index, elemGrp);
		getEventSupport().fireCollectionElementAddedEvent(PROPERTY_CHILDREN, elemGrp, index);
	}

	/**
	 *
	 */
	public JRDesignElementGroup removeElementGroup(JRDesignElementGroup elemGrp)
	{
		elemGrp.setElementGroup(null);

		int idx = this.children.indexOf(elemGrp);
		if (idx >= 0)
		{
			this.children.remove(idx);
			getEventSupport().fireCollectionElementRemovedEvent(PROPERTY_CHILDREN, elemGrp, idx);
		}
		
		return elemGrp;
	}

	/**
	 * 
	 */
	public Object clone()
	{
		JRDesignElementGroup clone = (JRDesignElementGroup)super.clone();
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
