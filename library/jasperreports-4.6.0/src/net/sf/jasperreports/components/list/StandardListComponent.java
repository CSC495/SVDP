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
package net.sf.jasperreports.components.list;

import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.Serializable;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRDatasetRun;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.type.PrintOrderEnum;
import net.sf.jasperreports.engine.util.JRCloneUtils;

/**
 * Standard {@link ListComponent} implementation.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: StandardListComponent.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class StandardListComponent implements Serializable, ListComponent, JRChangeEventsSupport
{

	private static final long serialVersionUID = 1L;
	
	public static final String PROPERTY_PRINT_ORDER = "printOrder";
	public static final String PROPERTY_IGNORE_WIDTH = "ignoreWidth";
	public static final String PROPERTY_DATASET_RUN = "datasetRun";
	
	private JRDatasetRun datasetRun;
	private ListContents contents;
	private PrintOrderEnum printOrderValue;
	private Boolean ignoreWidth;

	public StandardListComponent()
	{
	}

	public StandardListComponent(ListComponent list, JRBaseObjectFactory baseFactory)
	{
		this.datasetRun = baseFactory.getDatasetRun(list.getDatasetRun());
		this.contents = new BaseListContents(list.getContents(), baseFactory);
		this.printOrderValue = list.getPrintOrderValue();
		this.ignoreWidth = list.getIgnoreWidth();
	}
	
	public JRDatasetRun getDatasetRun()
	{
		return datasetRun;
	}
	
	/**
	 * Sets the subdataset run information that will be used by this list.
	 * 
	 * @param datasetRun the subdataset run information
	 * @see #getDatasetRun()
	 */
	public void setDatasetRun(JRDatasetRun datasetRun)
	{
		Object old = this.datasetRun;
		this.datasetRun = datasetRun;
		getEventSupport().firePropertyChange(PROPERTY_DATASET_RUN, old, this.datasetRun);
	}
	
	public ListContents getContents()
	{
		return contents;
	}
	
	/**
	 * Sets the list item contents.
	 * 
	 * @param contents the list item contents
	 * @see #getContents()
	 */
	public void setContents(ListContents contents)
	{
		this.contents = contents;
	}
	
	public Object clone()
	{
		StandardListComponent clone = null;
		try
		{
			clone = (StandardListComponent) super.clone();
		} 
		catch (CloneNotSupportedException e)
		{
			// never
			throw new JRRuntimeException(e);
		}
		clone.datasetRun = JRCloneUtils.nullSafeClone(datasetRun);
		clone.contents = JRCloneUtils.nullSafeClone(contents);
		clone.eventSupport = null;
		return clone;
	}

	/**
	 * 
	 */
	public PrintOrderEnum getPrintOrderValue()
	{
		return printOrderValue;
	}

	/**
	 * Sets the list cell print order.
	 * 
	 * @param printOrderValue the cell print oder, null or one of
	 * <ul>
	 * <li>{@link PrintOrderEnum#VERTICAL}</li>
	 * <li>{@link PrintOrderEnum#HORIZONTAL}</li>
	 * </ul>
	 * @see #getPrintOrderValue()
	 */
	public void setPrintOrderValue(PrintOrderEnum printOrderValue)
	{
		Object old = this.printOrderValue;
		this.printOrderValue = printOrderValue;
		getEventSupport().firePropertyChange(PROPERTY_PRINT_ORDER, old, this.printOrderValue);
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

	public Boolean getIgnoreWidth()
	{
		return ignoreWidth;
	}

	/**
	 * Sets the list ignore width flag.
	 * 
	 * @param ignoreWidth the ignore width flag
	 */
	public void setIgnoreWidth(Boolean ignoreWidth)
	{
		Object old = this.ignoreWidth;
		this.ignoreWidth = ignoreWidth;
		getEventSupport().firePropertyChange(PROPERTY_IGNORE_WIDTH, 
				old, this.ignoreWidth);
	}
	
	/**
	 * Sets the list ignore width flag.
	 * 
	 * @param ignoreWidth the ignore width flag
	 */
	public void setIgnoreWidth(boolean ignoreWidth)
	{
		setIgnoreWidth(Boolean.valueOf(ignoreWidth));
	}
	
	/*
	 * These fields are only for serialization backward compatibility.
	 */
	private int PSEUDO_SERIAL_VERSION_UID = JRConstants.PSEUDO_SERIAL_VERSION_UID; //NOPMD
	/**
	 * @deprecated
	 */
	private Byte printOrder;
	
	private void readObject(ObjectInputStream in) throws IOException, ClassNotFoundException
	{
		in.defaultReadObject();
		
		if (PSEUDO_SERIAL_VERSION_UID < JRConstants.PSEUDO_SERIAL_VERSION_UID_3_7_2)
		{
			printOrderValue = PrintOrderEnum.getByValue(printOrder);
		}
	}
	
}
