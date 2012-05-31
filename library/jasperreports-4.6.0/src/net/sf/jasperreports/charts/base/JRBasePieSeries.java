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
package net.sf.jasperreports.charts.base;

import java.io.Serializable;

import net.sf.jasperreports.charts.JRPieSeries;
import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRHyperlink;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.util.JRCloneUtils;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRBasePieSeries.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class JRBasePieSeries implements JRPieSeries, Serializable
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	protected JRExpression keyExpression;
	protected JRExpression valueExpression;
	protected JRExpression labelExpression;
	protected JRHyperlink sectionHyperlink;

	
	/**
	 *
	 */
	protected JRBasePieSeries()
	{
	}
	
	
	/**
	 *
	 */
	public JRBasePieSeries(JRPieSeries pieSeries, JRBaseObjectFactory factory)
	{
		factory.put(pieSeries, this);

		keyExpression = factory.getExpression(pieSeries.getKeyExpression());
		valueExpression = factory.getExpression(pieSeries.getValueExpression());
		labelExpression = factory.getExpression(pieSeries.getLabelExpression());
		sectionHyperlink = factory.getHyperlink(pieSeries.getSectionHyperlink());
	}

	
	/**
	 *
	 */
	public JRExpression getKeyExpression()
	{
		return keyExpression;
	}
		
	/**
	 *
	 */
	public JRExpression getValueExpression()
	{
		return valueExpression;
	}
		
	/**
	 *
	 */
	public JRExpression getLabelExpression()
	{
		return labelExpression;
	}

	
	public JRHyperlink getSectionHyperlink()
	{
		return sectionHyperlink;
	}
		
	/**
	 * 
	 */
	public Object clone() 
	{
		JRBasePieSeries clone = null;
		
		try
		{
			clone = (JRBasePieSeries)super.clone();
		}
		catch (CloneNotSupportedException e)
		{
			throw new JRRuntimeException(e);
		}
		
		clone.keyExpression = JRCloneUtils.nullSafeClone(keyExpression);
		clone.valueExpression = JRCloneUtils.nullSafeClone(valueExpression);
		clone.labelExpression = JRCloneUtils.nullSafeClone(labelExpression);
		clone.sectionHyperlink = JRCloneUtils.nullSafeClone(sectionHyperlink);
		
		return clone;
	}
}
