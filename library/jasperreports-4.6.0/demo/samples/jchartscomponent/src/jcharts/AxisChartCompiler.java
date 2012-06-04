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
package jcharts;

import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.component.Component;
import net.sf.jasperreports.engine.component.ComponentCompiler;
import net.sf.jasperreports.engine.design.JRVerifier;
import net.sf.jasperreports.engine.design.JasperDesign;
import net.sf.jasperreports.engine.type.EvaluationTimeEnum;

/**
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: AxisChartCompiler.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class AxisChartCompiler implements ComponentCompiler
{

	public void collectExpressions(Component component, JRExpressionCollector collector)
	{
		AxisChartComponent chart = (AxisChartComponent) component;
		collector.addExpression(chart.getLegendLabelExpression());
		collectExpressions(chart.getDataset(), collector);
	}

	public static void collectExpressions(AxisDataset dataset, JRExpressionCollector collector)
	{
		collector.collect(dataset);
		
		JRExpressionCollector datasetCollector = collector.getCollector(dataset);
		datasetCollector.addExpression(dataset.getLabelExpression());
		datasetCollector.addExpression(dataset.getValueExpression());
	}

	public void verify(Component component, JRVerifier verifier)
	{
		AxisChartComponent chart = (AxisChartComponent) component;
		
		if (chart.getAreaColor() == null)
		{
			verifier.addBrokenRule("No area color set for axis chart", chart);
		}
		
		verifyEvaluation(verifier, chart);
		
		verifyLegendLabelExpression(verifier, chart);
		
		AxisDataset dataset = chart.getDataset();
		if (dataset == null)
		{
			verifier.addBrokenRule("No dataset for axis chart", chart);
		}
		else
		{
			verify(verifier, dataset);
		}
	}

	protected void verifyEvaluation(JRVerifier verifier,
			AxisChartComponent chart)
	{
		EvaluationTimeEnum evaluationTime = chart.getEvaluationTime();
		if (evaluationTime == EvaluationTimeEnum.AUTO)
		{
			verifier.addBrokenRule("Axis chart evaluation time cannot be Auto", chart);
		}
		else if (evaluationTime == EvaluationTimeEnum.GROUP)
		{
			String groupName = chart.getEvaluationGroup();
			if (groupName == null)
			{
				verifier.addBrokenRule("Evaluation group not set for axis chart", chart);
			}
			else
			{
				JasperDesign report = verifier.getReportDesign();
				if (!report.getGroupsMap().containsKey(groupName))
				{
					verifier.addBrokenRule("Axis chart evaluation group " + groupName 
							+ " not found in report", chart);
				}
			}
		}
	}

	protected void verifyLegendLabelExpression(JRVerifier verifier,
			AxisChartComponent chart)
	{
		JRExpression legendLabelExpression = chart.getLegendLabelExpression();
		if (legendLabelExpression == null)
		{
			verifier.addBrokenRule("No legend label expression for axis chart", chart);
		}
		else
		{
			String valueClass = legendLabelExpression.getValueClassName();
			if (valueClass == null)
			{
				verifier.addBrokenRule("No value class for axis chart legend label expression", 
						legendLabelExpression);
			}
			else if (!"java.lang.String".equals(valueClass))
			{
				verifier.addBrokenRule("Class " + valueClass 
						+ " not supported for axis chart legend label expression. Use java.lang.String instead.",
						legendLabelExpression);
			}
		}
	}

	protected void verify(JRVerifier verifier, AxisDataset dataset)
	{
		verifier.verifyElementDataset(dataset);
		
		JRExpression labelExpression = dataset.getLabelExpression();
		if (labelExpression == null)
		{
			verifier.addBrokenRule("No label expression for axis chart dataset", dataset);
		}
		else
		{
			String valueClass = labelExpression.getValueClassName();
			if (valueClass == null)
			{
				verifier.addBrokenRule("No value class for axis chart dataset label expression", 
						labelExpression);
			}
			else if (!"java.lang.String".equals(valueClass))
			{
				verifier.addBrokenRule("Class " + valueClass 
						+ " not supported for axis chart dataset label expression. Use java.lang.String instead.",
						labelExpression);
			}
		}
		
		JRExpression valueExpression = dataset.getValueExpression();
		if (valueExpression == null)
		{
			verifier.addBrokenRule("No value expression for axis chart dataset", dataset);
		}
		else
		{
			String valueClass = valueExpression.getValueClassName();
			if (valueClass == null)
			{
				verifier.addBrokenRule("No value class for axis chart dataset value expression", 
						valueExpression);
			}
			else if (!"java.lang.Double".equals(valueClass))
			{
				verifier.addBrokenRule("Class " + valueClass 
						+ " not supported for axis chart dataset value expression. Use java.lang.Double instead.",
						valueExpression);
			}
		}
	}

	public Component toCompiledComponent(Component component,
			JRBaseObjectFactory baseFactory)
	{
		AxisChartComponent chart = (AxisChartComponent) component;
		AxisChartComponent compiledChart = new AxisChartComponent(chart, baseFactory);
		return compiledChart;
	}
	
}
