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
package net.sf.jasperreports.components.spiderchart;

import java.awt.BasicStroke;
import java.awt.Color;
import java.awt.Font;
import java.awt.geom.Rectangle2D;
import java.util.Locale;
import java.util.TimeZone;

import net.sf.jasperreports.charts.type.EdgeEnum;
import net.sf.jasperreports.charts.util.ChartUtil;
import net.sf.jasperreports.components.charts.ChartCustomizer;
import net.sf.jasperreports.components.charts.ChartSettings;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRComponentElement;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.Renderable;
import net.sf.jasperreports.engine.util.JRFontUtil;

import org.jfree.chart.JFreeChart;
import org.jfree.chart.labels.StandardCategoryItemLabelGenerator;
import org.jfree.chart.labels.StandardCategoryToolTipGenerator;
import org.jfree.chart.plot.SpiderWebPlot;
import org.jfree.chart.title.LegendTitle;
import org.jfree.chart.title.TextTitle;
import org.jfree.data.category.DefaultCategoryDataset;
import org.jfree.ui.RectangleEdge;

/**
 * Spider Chart design evaluator.
 * 
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: SpiderChartRendererEvaluator.java 5397 2012-05-21 01:10:02Z teodord $
 */
public class SpiderChartRendererEvaluator
{

	/**
	 *
	 */
	public static final String FILL_DATASET = "fillDataset";
	public static final String SAMPLE_DATASET = "sampleDataset";
	
	public static final Double SAMPLE_MAXVALUE = Double.valueOf(10d);
	
	private static DefaultCategoryDataset sampleDataset;
	
	/**
	 * @deprecated Replaced by {@link #evaluateRenderable(JasperReportsContext, JRComponentElement, SpiderChartSharedBean, ChartCustomizer, String, String)}. 
	 */
	public static net.sf.jasperreports.engine.JRRenderable evaluateRenderer(
		JRComponentElement element, 
		SpiderChartSharedBean spiderchartBean, 
		ChartCustomizer chartCustomizer, 
		String defaultRenderType,
		String datasetType
		)
	{
		return evaluateRenderable(DefaultJasperReportsContext.getInstance(), element, spiderchartBean, chartCustomizer, defaultRenderType, datasetType);
	}
	
	/**
	 * 
	 */
	public static Renderable evaluateRenderable(
		JasperReportsContext jasperReportsContext,
		JRComponentElement element, 
		SpiderChartSharedBean spiderchartBean, 
		ChartCustomizer chartCustomizer, 
		String defaultRenderType,
		String datasetType
		)
	{
		SpiderChartComponent chartComponent = (SpiderChartComponent) element.getComponent();
		ChartSettings chartSettings = chartComponent.getChartSettings();
		SpiderPlot plot = (SpiderPlot)chartComponent.getPlot();
		
		DefaultCategoryDataset dataset = null;
		StandardCategoryItemLabelGenerator labelGenerator = null;
		
		if(FILL_DATASET.equals(datasetType))
		{
			dataset = ((FillSpiderDataset)spiderchartBean.getDataset()).getCustomDataset();
			labelGenerator = ((FillSpiderDataset)spiderchartBean.getDataset()).getLabelGenerator();
		}
		else
		{
			dataset = getSampleDataset();
			labelGenerator = new StandardCategoryItemLabelGenerator();
		}
		
		SpiderWebPlot spiderWebPlot = new SpiderWebPlot(dataset);

		if(plot.getAxisLineColor() != null)
		{
			spiderWebPlot.setAxisLinePaint(plot.getAxisLineColor());
		}
		if(plot.getAxisLineWidth() != null)
		{
			spiderWebPlot.setAxisLineStroke(new BasicStroke(plot.getAxisLineWidth()));
		}
		if(plot.getBackcolor() != null)
		{
			spiderWebPlot.setBackgroundPaint(plot.getBackcolor());
		}
		if(plot.getBackgroundAlpha() != null)
		{
			spiderWebPlot.setBackgroundAlpha(plot.getBackgroundAlpha());
		}
		if(plot.getForegroundAlpha() != null)
		{
			spiderWebPlot.setForegroundAlpha(plot.getForegroundAlpha());
		}
		if(plot.getHeadPercent() != null)
		{
			spiderWebPlot.setHeadPercent(plot.getHeadPercent());
		}
		if(plot.getInteriorGap() != null)
		{
			spiderWebPlot.setInteriorGap(plot.getInteriorGap());
		}
		if(plot.getLabelColor() != null)
		{
			spiderWebPlot.setLabelPaint(plot.getLabelColor());
		}
		if(plot.getLabelFont() != null)
		{
			spiderWebPlot.setLabelFont(JRFontUtil.getAwtFont(plot.getLabelFont(), Locale.getDefault()));
		}
		if(plot.getLabelGap() != null)
		{
			spiderWebPlot.setAxisLabelGap(plot.getLabelGap());
		}
		if(spiderchartBean.getMaxValue() != null)
		{
			spiderWebPlot.setMaxValue(spiderchartBean.getMaxValue());
		}
		if(plot.getRotation() != null)
		{
			spiderWebPlot.setDirection(plot.getRotation().getRotation());
		}
		if(plot.getStartAngle() != null)
		{
			spiderWebPlot.setStartAngle(plot.getStartAngle());
		}
		if(plot.getTableOrder() != null)
		{
			spiderWebPlot.setDataExtractOrder(plot.getTableOrder().getOrder());
		}
		if(plot.getWebFilled() != null)
		{
			spiderWebPlot.setWebFilled(plot.getWebFilled());
		}

		spiderWebPlot.setToolTipGenerator(new StandardCategoryToolTipGenerator());
		spiderWebPlot.setLabelGenerator(labelGenerator);
		
		Font titleFont = chartSettings.getTitleFont() != null 
			? JRFontUtil.getAwtFont(chartSettings.getTitleFont(), Locale.getDefault())
			: TextTitle.DEFAULT_FONT;

		String titleText = spiderchartBean.getTitleText();
		
		JFreeChart jfreechart = new JFreeChart(titleText, titleFont, spiderWebPlot, true);

		Color backcolor = chartSettings.getBackcolor() != null ? chartSettings.getBackcolor() : element.getBackcolor();
		if(backcolor != null)
		{
			jfreechart.setBackgroundPaint(backcolor);
		}
		
		RectangleEdge titleEdge = getEdge(chartSettings.getTitlePosition(), RectangleEdge.TOP);
		
		if (titleText != null)
		{
			TextTitle title = jfreechart.getTitle();
			title.setText(titleText);
			if(chartSettings.getTitleColor() != null)
			{
				title.setPaint(chartSettings.getTitleColor());
			}
			
			title.setFont(titleFont);
			title.setPosition(titleEdge);
			jfreechart.setTitle(title);
		}

		String subtitleText = spiderchartBean.getSubtitleText();
		if (subtitleText != null)
		{
			TextTitle subtitle = new TextTitle(subtitleText);
			subtitle.setText(subtitleText);
			if(chartSettings.getSubtitleColor() != null)
			{
				subtitle.setPaint(chartSettings.getSubtitleColor());
			}

			if(chartSettings.getSubtitleColor() != null)
			{
				Font subtitleFont = chartSettings.getSubtitleFont() != null 
				? JRFontUtil.getAwtFont(chartSettings.getSubtitleFont(), Locale.getDefault())
				: TextTitle.DEFAULT_FONT;
				subtitle.setFont(subtitleFont);
			}
			
			subtitle.setPosition(titleEdge);

			jfreechart.addSubtitle(subtitle);
		}

		// Apply all of the legend formatting options
		LegendTitle legend = jfreechart.getLegend();

		if (legend != null)
		{
			legend.setVisible((chartSettings.getShowLegend() == null || chartSettings.getShowLegend()));
			if (legend.isVisible())
			{
				if(chartSettings.getLegendColor() != null)
				{
					legend.setItemPaint(chartSettings.getLegendColor());
				}
				if (chartSettings.getLegendBackgroundColor() != null)
				{
					legend.setBackgroundPaint(chartSettings.getLegendBackgroundColor());
				}
	
				if(chartSettings.getLegendFont() != null)
				{
					legend.setItemFont(JRFontUtil.getAwtFont(chartSettings.getLegendFont(), Locale.getDefault()));
				}
				legend.setPosition(getEdge(chartSettings.getLegendPosition(), RectangleEdge.BOTTOM));
			}
		}

		String renderType = chartSettings.getRenderType() == null ? defaultRenderType : chartSettings.getRenderType();
		Rectangle2D rectangle = new Rectangle2D.Double(0,0,element.getWidth(),element.getHeight());
		
		if (chartCustomizer != null)
		{
			chartCustomizer.customize(jfreechart, chartComponent);
		}
		
		return 
			ChartUtil.getInstance(jasperReportsContext).getChartRenderableFactory(renderType).getRenderable(
				jasperReportsContext,
				jfreechart, 
				spiderchartBean.getHyperlinkProvider(),
				rectangle
				);
	}
	
	public static DefaultCategoryDataset getSampleDataset()
	{
		if (sampleDataset == null)
		{
			DefaultCategoryDataset dataset = new DefaultCategoryDataset();
			dataset.addValue(1.0, "Series 1", "Category 1");
			dataset.addValue(5.0, "Series 1", "Category 2");
			dataset.addValue(4.0, "Series 1", "Category 3");
			dataset.addValue(3.0, "Series 1", "Category 4");
			dataset.addValue(6.0, "Series 1", "Category 5");
			dataset.addValue(4.0, "Series 1", "Category 6");
			dataset.addValue(3.0, "Series 1", "Category 7");
			dataset.addValue(5.0, "Series 2", "Category 1");
			dataset.addValue(7.0, "Series 2", "Category 2");
			dataset.addValue(8.0, "Series 2", "Category 3");
			dataset.addValue(6.0, "Series 2", "Category 4");
			dataset.addValue(9.0, "Series 2", "Category 5");
			dataset.addValue(8.0, "Series 2", "Category 6");
			dataset.addValue(7.0, "Series 2", "Category 7");
			dataset.addValue(5.0, "Series 3", "Category 1");
			dataset.addValue(4.0, "Series 3", "Category 2");
			dataset.addValue(6.0, "Series 3", "Category 3");
			dataset.addValue(3.0, "Series 3", "Category 4");
			dataset.addValue(2.0, "Series 3", "Category 5");
			dataset.addValue(7.0, "Series 3", "Category 6");
			dataset.addValue(5.0, "Series 3", "Category 7");
			
			sampleDataset = dataset;
		}
		
		return sampleDataset;
	}

	public Locale getLocale() {
		return null;//FIXMETHEME
	}

	public TimeZone getTimeZone() {
		return null;
	}

	/**
	 *
	 */
	private static RectangleEdge getEdge(EdgeEnum position, RectangleEdge defaultPosition)
	{
		RectangleEdge edge = defaultPosition;
		if(position != null)
		{
			switch (position)
			{
				case TOP :
				{
					edge = RectangleEdge.TOP;
					break;
				}
				case BOTTOM :
				{
					edge = RectangleEdge.BOTTOM;
					break;
				}
				case LEFT :
				{
					edge = RectangleEdge.LEFT;
					break;
				}
				case RIGHT :
				{
					edge = RectangleEdge.RIGHT;
					break;
				}
			}
		}
		return edge;
	}

}
