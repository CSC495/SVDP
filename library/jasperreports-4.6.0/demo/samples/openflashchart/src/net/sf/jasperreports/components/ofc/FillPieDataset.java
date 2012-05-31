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
package net.sf.jasperreports.components.ofc;

import java.util.ArrayList;
import java.util.List;

import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.fill.JRCalculator;
import net.sf.jasperreports.engine.fill.JRExpressionEvalException;
import net.sf.jasperreports.engine.fill.JRFillElementDataset;
import net.sf.jasperreports.engine.fill.JRFillObjectFactory;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: FillPieDataset.java 5249 2012-04-10 12:11:15Z teodord $
 */
public class FillPieDataset extends JRFillElementDataset
{

	private final PieDataset dataset;
	
	private String key;
	private Number value;
	
	private List<String> keys;
	private List<Number> values;
	
	public FillPieDataset(PieDataset dataset,	JRFillObjectFactory factory)
	{
		super(dataset, factory);
		
		this.dataset = dataset;
	}

	protected void customEvaluate(JRCalculator calculator)
			throws JRExpressionEvalException
	{
		key = (String) calculator.evaluate(dataset.getKeyExpression());
		value = (Number) calculator.evaluate(dataset.getValueExpression());
	}

	protected void customIncrement()
	{
		keys.add(key);
		values.add(value);
	}

	protected void customInitialize()
	{
		keys = new ArrayList<String>();
		values = new ArrayList<Number>();
	}

	public void collectExpressions(JRExpressionCollector collector)
	{
		PieChartCompiler.collectExpressions(dataset, collector);
	}

	public void finishDataset()
	{
		super.increment();
	}
	
	public List<String> getKeys()
	{
		return keys;
	}

	public List<Number> getValues()
	{
		return values;
	}

}
