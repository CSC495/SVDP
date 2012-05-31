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

import net.sf.jasperreports.engine.type.CalculationEnum;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRComparableIncrementerFactory.java 5213 2012-04-03 14:44:47Z teodord $
 */
public final class JRComparableIncrementerFactory extends JRAbstractExtendedIncrementerFactory
{


	/**
	 *
	 */
	private static JRComparableIncrementerFactory mainInstance = new JRComparableIncrementerFactory();


	/**
	 *
	 */
	private JRComparableIncrementerFactory()
	{
	}


	/**
	 *
	 */
	public static JRComparableIncrementerFactory getInstance()
	{
		return mainInstance;
	}


	/**
	 *
	 */
	public JRExtendedIncrementer getExtendedIncrementer(CalculationEnum calculation)
	{
		JRExtendedIncrementer incrementer = null;

		switch (calculation)
		{
			case LOWEST :
			{
				incrementer = JRComparableLowestIncrementer.getInstance();
				break;
			}
			case HIGHEST :
			{
				incrementer = JRComparableHighestIncrementer.getInstance();
				break;
			}
			case SYSTEM :
			case NOTHING :
			case COUNT :
			case SUM :
			case AVERAGE :
			case STANDARD_DEVIATION :
			case VARIANCE :
			case FIRST :
			case DISTINCT_COUNT :
			default :
			{
				incrementer = JRDefaultIncrementerFactory.getInstance().getExtendedIncrementer(calculation);
				break;
			}
		}
		
		return incrementer;
	}
}


/**
 *
 */
final class JRComparableLowestIncrementer extends JRAbstractExtendedIncrementer
{
	/**
	 *
	 */
	private static JRComparableLowestIncrementer mainInstance = new JRComparableLowestIncrementer();

	/**
	 *
	 */
	private JRComparableLowestIncrementer()
	{
	}

	/**
	 *
	 */
	public static JRComparableLowestIncrementer getInstance()
	{
		return mainInstance;
	}

	/**
	 *
	 */
	@SuppressWarnings({ "rawtypes", "unchecked" })
	public Object increment(
		JRCalculable variable, 
		Object expressionValue,
		AbstractValueProvider valueProvider
		)
	{
		Comparable value = (Comparable)variable.getIncrementedValue();
		Comparable newValue = (Comparable)expressionValue;

		if (
			value != null && !variable.isInitialized() &&
			(newValue == null || value.compareTo(newValue) < 0)
			)
		{
			newValue = value;
		}
				
		return newValue;
	}

	
	public Object initialValue()
	{
		return null;
	}
}


/**
 *
 */
final class JRComparableHighestIncrementer extends JRAbstractExtendedIncrementer
{
	/**
	 *
	 */
	private static JRComparableHighestIncrementer mainInstance = new JRComparableHighestIncrementer();

	/**
	 *
	 */
	private JRComparableHighestIncrementer()
	{
	}

	/**
	 *
	 */
	public static JRComparableHighestIncrementer getInstance()
	{
		return mainInstance;
	}

	/**
	 *
	 */
	@SuppressWarnings({ "rawtypes", "unchecked" })
	public Object increment(
		JRCalculable variable, 
		Object expressionValue,
		AbstractValueProvider valueProvider
		)
	{
		Comparable value = (Comparable)variable.getIncrementedValue();
		Comparable newValue = (Comparable)expressionValue;

		if (
			value != null && !variable.isInitialized() &&
			(newValue == null || value.compareTo(newValue) > 0)
			)
		{
			newValue = value;
		}
				
		return newValue;
	}

	
	public Object initialValue()
	{
		return null;
	}
}
