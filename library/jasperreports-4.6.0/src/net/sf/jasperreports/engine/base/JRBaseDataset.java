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
import java.util.UUID;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRField;
import net.sf.jasperreports.engine.JRGroup;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JRPropertiesHolder;
import net.sf.jasperreports.engine.JRPropertiesMap;
import net.sf.jasperreports.engine.JRQuery;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRScriptlet;
import net.sf.jasperreports.engine.JRSortField;
import net.sf.jasperreports.engine.JRVariable;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.type.WhenResourceMissingTypeEnum;
import net.sf.jasperreports.engine.util.JRCloneUtils;

/**
 * The base implementation of {@link net.sf.jasperreports.engine.JRDataset JRDataset}.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRBaseDataset.java 5337 2012-05-04 09:15:58Z lucianc $
 */
public class JRBaseDataset implements JRDataset, Serializable, JRChangeEventsSupport
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	public static final String PROPERTY_WHEN_RESOURCE_MISSING_TYPE = "whenResourceMissingType";

	protected final boolean isMain;
	protected UUID uuid;
	protected String name;
	protected String scriptletClass;
	protected JRScriptlet[] scriptlets;
	protected JRParameter[] parameters;
	protected JRQuery query;
	protected JRField[] fields;
	protected JRSortField[] sortFields;
	protected JRVariable[] variables;
	protected JRGroup[] groups;
	protected String resourceBundle;
	protected WhenResourceMissingTypeEnum whenResourceMissingTypeValue = WhenResourceMissingTypeEnum.NULL;
	protected JRPropertiesMap propertiesMap;
	protected JRExpression filterExpression;
	
	protected JRBaseDataset(boolean isMain)
	{
		this.isMain = isMain;
		
		propertiesMap = new JRPropertiesMap();
	}
	
	protected JRBaseDataset(JRDataset dataset, JRBaseObjectFactory factory)
	{
		factory.put(dataset, this);
		
		uuid = dataset.getUUID();
		name = dataset.getName();
		scriptletClass = dataset.getScriptletClass();
		resourceBundle = dataset.getResourceBundle();
		whenResourceMissingTypeValue = dataset.getWhenResourceMissingTypeValue();

		/*   */
		this.propertiesMap = dataset.getPropertiesMap().cloneProperties();

		query = factory.getQuery(dataset.getQuery());

		isMain = dataset.isMainDataset();
		
		/*   */
		JRScriptlet[] jrScriptlets = dataset.getScriptlets();
		if (jrScriptlets != null && jrScriptlets.length > 0)
		{
			scriptlets = new JRScriptlet[jrScriptlets.length];
			for(int i = 0; i < scriptlets.length; i++)
			{
				scriptlets[i] = factory.getScriptlet(jrScriptlets[i]);
			}
		}
		
		/*   */
		JRParameter[] jrParameters = dataset.getParameters();
		if (jrParameters != null && jrParameters.length > 0)
		{
			parameters = new JRParameter[jrParameters.length];
			for(int i = 0; i < parameters.length; i++)
			{
				parameters[i] = factory.getParameter(jrParameters[i]);
			}
		}
		
		/*   */
		JRField[] jrFields = dataset.getFields();
		if (jrFields != null && jrFields.length > 0)
		{
			fields = new JRField[jrFields.length];
			for(int i = 0; i < fields.length; i++)
			{
				fields[i] = factory.getField(jrFields[i]);
			}
		}

		/*   */
		JRSortField[] jrSortFields = dataset.getSortFields();
		if (jrSortFields != null && jrSortFields.length > 0)
		{
			sortFields = new JRSortField[jrSortFields.length];
			for(int i = 0; i < sortFields.length; i++)
			{
				sortFields[i] = factory.getSortField(jrSortFields[i]);
			}
		}

		/*   */
		JRVariable[] jrVariables = dataset.getVariables();
		if (jrVariables != null && jrVariables.length > 0)
		{
			variables = new JRVariable[jrVariables.length];
			for(int i = 0; i < variables.length; i++)
			{
				variables[i] = factory.getVariable(jrVariables[i]);
			}
		}

		/*   */
		JRGroup[] jrGroups = dataset.getGroups();
		if (jrGroups != null && jrGroups.length > 0)
		{
			groups = new JRGroup[jrGroups.length];
			for(int i = 0; i < groups.length; i++)
			{
				groups[i] = factory.getGroup(jrGroups[i]);
			}
		}
		
		filterExpression = factory.getExpression(dataset.getFilterExpression());
	}

	
	public UUID getUUID()
	{
		if (uuid == null)
		{
			uuid = UUID.randomUUID();
		}
		return uuid;
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
	public String getScriptletClass()
	{
		return scriptletClass;
	}

	/**
	 *
	 */
	public JRQuery getQuery()
	{
		return query;
	}

	/**
	 *
	 */
	public JRScriptlet[] getScriptlets()
	{
		return scriptlets;
	}

	/**
	 *
	 */
	public JRParameter[] getParameters()
	{
		return parameters;
	}

	/**
	 *
	 */
	public JRField[] getFields()
	{
		return fields;
	}

	/**
	 *
	 */
	public JRSortField[] getSortFields()
	{
		return sortFields;
	}

	/**
	 *
	 */
	public JRVariable[] getVariables()
	{
		return variables;
	}

	/**
	 *
	 */
	public JRGroup[] getGroups()
	{
		return groups;
	}

	public boolean isMainDataset()
	{
		return isMain;
	}

	public String getResourceBundle()
	{
		return resourceBundle;
	}

	public WhenResourceMissingTypeEnum getWhenResourceMissingTypeValue()
	{
		return whenResourceMissingTypeValue;
	}

	public void setWhenResourceMissingType(WhenResourceMissingTypeEnum whenResourceMissingTypeValue)
	{
		Object old = this.whenResourceMissingTypeValue;
		this.whenResourceMissingTypeValue = whenResourceMissingTypeValue;
		getEventSupport().firePropertyChange(PROPERTY_WHEN_RESOURCE_MISSING_TYPE, old, this.whenResourceMissingTypeValue);
	}

	public boolean hasProperties()
	{
		return propertiesMap != null && propertiesMap.hasProperties();
	}

	public JRPropertiesMap getPropertiesMap()
	{
		return propertiesMap;
	}

	public JRPropertiesHolder getParentProperties()
	{
		return null;
	}

	public JRExpression getFilterExpression()
	{
		return filterExpression;
	}
	
	/**
	 * 
	 */
	public Object clone() 
	{
		JRBaseDataset clone = null;

		try
		{
			clone = (JRBaseDataset)super.clone();
		}
		catch (CloneNotSupportedException e)
		{
			throw new JRRuntimeException(e);
		}
		
		clone.query = JRCloneUtils.nullSafeClone(query);
		clone.filterExpression = JRCloneUtils.nullSafeClone(filterExpression);
		if (propertiesMap != null)
		{
			clone.propertiesMap = (JRPropertiesMap)propertiesMap.clone();
		}

		clone.parameters = JRCloneUtils.cloneArray(parameters);
		clone.fields = JRCloneUtils.cloneArray(fields);
		clone.sortFields = JRCloneUtils.cloneArray(sortFields);
		clone.variables = JRCloneUtils.cloneArray(variables);
		clone.groups = JRCloneUtils.cloneArray(groups);
		
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

	
	/*
	 * These fields are only for serialization backward compatibility.
	 */
	private int PSEUDO_SERIAL_VERSION_UID = JRConstants.PSEUDO_SERIAL_VERSION_UID; //NOPMD
	/**
	 * @deprecated
	 */
	private byte whenResourceMissingType;
	
	private void readObject(ObjectInputStream in) throws IOException, ClassNotFoundException
	{
		in.defaultReadObject();
		
		if (PSEUDO_SERIAL_VERSION_UID < JRConstants.PSEUDO_SERIAL_VERSION_UID_3_7_2)
		{
			whenResourceMissingTypeValue = WhenResourceMissingTypeEnum.getByValue(whenResourceMissingType);
		}
	}

}
