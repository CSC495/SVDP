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
package net.sf.jasperreports.engine.design.events;

import java.beans.PropertyChangeEvent;

import net.sf.jasperreports.engine.JRConstants;


/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: CollectionElementRemovedEvent.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class CollectionElementRemovedEvent extends PropertyChangeEvent
{

	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	private final int removedIndex;
	
	public CollectionElementRemovedEvent(Object source, String propertyName,  
			Object removedValue, int removedIndex)
	{
		super(source, propertyName, removedValue, null);
		
		this.removedIndex = removedIndex;
	}

	public Object getRemovedValue()
	{
		return getOldValue();
	}
	
	public int getRemovedIndex()
	{
		return removedIndex;
	}

}
