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
package net.sf.jasperreports.charts.fill;

import java.util.Date;

import net.sf.jasperreports.charts.JRTimeSeries;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRHyperlink;
import net.sf.jasperreports.engine.JRHyperlinkHelper;
import net.sf.jasperreports.engine.JRPrintHyperlink;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.fill.JRCalculator;
import net.sf.jasperreports.engine.fill.JRExpressionEvalException;
import net.sf.jasperreports.engine.fill.JRFillHyperlinkHelper;
import net.sf.jasperreports.engine.fill.JRFillObjectFactory;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRFillTimeSeries.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRFillTimeSeries implements JRTimeSeries
{

	/**
	 *
	 */
	protected JRTimeSeries parent;

	private Comparable<?> series;
	private Date timePeriod;
	private Number value;
	private String label;
	private JRPrintHyperlink itemHyperlink;
	
	
	/**
	 *
	 */
	public JRFillTimeSeries(
		JRTimeSeries timeSeries, 
		JRFillObjectFactory factory
		)
	{
		factory.put(timeSeries, this);

		parent = timeSeries;
	}


	/**
	 *
	 */
	public JRExpression getSeriesExpression()
	{
		return parent.getSeriesExpression();
	}
		
	/**
	 *
	 */
	public JRExpression getTimePeriodExpression()
	{
		return parent.getTimePeriodExpression();
	}
		
	/**
	 *
	 */
	public JRExpression getValueExpression()
	{
		return parent.getValueExpression();
	}
		
	/**
	 *
	 */
	public JRExpression getLabelExpression()
	{
		return parent.getLabelExpression();
	}
	
	
	/**
	 *
	 */
	protected Comparable<?> getSeries()
	{
		return series;
	}
		
	/**
	 *
	 */
	protected Date getTimePeriod()
	{
		return timePeriod;
	}
		
	/**
	 *
	 */
	protected Number getValue()
	{
		return value;
	}
		
	/**
	 *
	 */
	protected String getLabel()
	{
		return label;
	}
	
	
	/**
	 *
	 */
	protected void evaluate(JRCalculator calculator) throws JRExpressionEvalException
	{
		series = (Comparable<?>)calculator.evaluate(getSeriesExpression()); 
		timePeriod = (Date)calculator.evaluate(getTimePeriodExpression()); 
		value = (Number)calculator.evaluate(getValueExpression());
		label = (String)calculator.evaluate(getLabelExpression());
	
		if (hasItemHyperlink())
		{
			evaluateItemHyperlink(calculator);
		}
	}


	protected void evaluateItemHyperlink(JRCalculator calculator) throws JRExpressionEvalException
	{
		try
		{
			itemHyperlink = JRFillHyperlinkHelper.evaluateHyperlink(getItemHyperlink(), calculator, JRExpression.EVALUATION_DEFAULT);
		}
		catch (JRExpressionEvalException e)
		{
			throw e;
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
	}


	public JRHyperlink getItemHyperlink()
	{
		return parent.getItemHyperlink();
	}


	public boolean hasItemHyperlink()
	{
		return !JRHyperlinkHelper.isEmpty(getItemHyperlink()); 
	}

	
	public JRPrintHyperlink getPrintItemHyperlink()
	{
		return itemHyperlink;
	}

	/**
	 *
	 */
	public Object clone() 
	{
		throw new UnsupportedOperationException();
	}
}
