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

import java.awt.Color;
import java.util.ArrayList;
import java.util.List;
import java.util.ListIterator;

import net.sf.jasperreports.charts.type.EdgeEnum;
import net.sf.jasperreports.components.charts.ChartSettings;
import net.sf.jasperreports.engine.JRAnchor;
import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.JRFont;
import net.sf.jasperreports.engine.JRHyperlinkHelper;
import net.sf.jasperreports.engine.JRHyperlinkParameter;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.design.JRDesignHyperlink;
import net.sf.jasperreports.engine.design.events.JRChangeEventsSupport;
import net.sf.jasperreports.engine.design.events.JRPropertyChangeSupport;
import net.sf.jasperreports.engine.type.HyperlinkTypeEnum;
import net.sf.jasperreports.engine.util.JRCloneUtils;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: StandardChartSettings.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class StandardChartSettings implements ChartSettings, JRChangeEventsSupport
{


	/**
	 *
	 */
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;
	
	/*
	 * Chart properties
	 */
	
	public static final byte CHART_TYPE_SPIDER = 22;
	
	public static final String PROPERTY_LEGEND_BACKGROUND_COLOR = "legendBackgroundColor";
	
	public static final String PROPERTY_LEGEND_COLOR = "legendColor";
	
	public static final String PROPERTY_LEGEND_POSITION = "legendPosition";
	
	public static final String PROPERTY_SHOW_LEGEND = "showLegend";
	
	public static final String PROPERTY_SUBTITLE_COLOR = "subtitleColor";
	
	public static final String PROPERTY_TITLE_COLOR = "titleColor";
	
	public static final String PROPERTY_TITLE_POSITION = "titlePosition";
	
	public static final String PROPERTY_RENDER_TYPE = "renderType";
	
	public static final String PROPERTY_BOOKMARK_LEVEL = "bookmarkLevel";
	
	public static final String PROPERTY_BACKCOLOR = "backcolor";
	
	public static final String PROPERTY_ANCHOR_NAME_EXPRESSION = "anchorNameExpression";
	
	public static final String PROPERTY_EVALUATION_GROUP = "evaluationGroup";
	
	public static final String PROPERTY_EVALUATION_TIME = "evaluationTime";
	
	public static final String PROPERTY_CHART_TYPE = "chartType";
	
	public static final String PROPERTY_LEGEND_FONT = "legendFont";
	
	public static final String PROPERTY_SUBTITLE_EXPRESSION = "subtitleExpression";
	
	public static final String PROPERTY_SUBTITLE_FONT = "subtitleFont";
	
	public static final String PROPERTY_TITLE_EXPRESSION = "titleExpression";
	
	public static final String PROPERTY_TITLE_FONT = "titleFont";
	
	public static final String PROPERTY_HYPERLINK_ANCHOR_EXPRESSION = "hyperlinkAnchorExpression";
	
	public static final String PROPERTY_HYPERLINK_PAGE_EXPRESSION = "hyperlinkPageExpression";
	
	public static final String PROPERTY_HYPERLINK_REFERENCE_EXPRESSION = "hyperlinkReferenceExpression";
	
	public static final String PROPERTY_HYPERLINK_TARGET = "hyperlinkTarget";
	
	public static final String PROPERTY_LINK_TARGET = "linkTarget";
	
	public static final String PROPERTY_HYPERLINK_TOOLTIP_EXPRESSION = "hyperlinkTooltipExpression";
	
	public static final String PROPERTY_LINK_TYPE = "linkType";
	
	public static final String PROPERTY_HYPERLINK_PARAMETERS = "hyperlinkParameters";
	
	public static final String PROPERTY_CUSTOMIZER_CLASS = "customizerClass";
	
	
	/**
	 *
	 */
	protected Byte chartType = CHART_TYPE_SPIDER;

	/**
	 *
	 */
	protected Boolean showLegend;
	protected String linkType;
	protected String linkTarget;
	protected List<JRHyperlinkParameter> hyperlinkParameters = new ArrayList<JRHyperlinkParameter>();
	protected Color backcolor;
	
	protected Color titleColor;
	protected Color subtitleColor;
	protected Color legendColor;
	protected Color legendBackgroundColor;
	protected EdgeEnum legendPosition;
	protected EdgeEnum titlePosition;

	protected String renderType;

	/**
	 *
	 */
	protected JRFont titleFont;
	protected JRFont subtitleFont;
	protected JRFont legendFont;

	/**
	 *
	 */
	protected JRExpression titleExpression;
	protected JRExpression subtitleExpression;
	protected JRExpression anchorNameExpression;
	protected JRExpression hyperlinkReferenceExpression;
	protected JRExpression hyperlinkAnchorExpression;
	protected JRExpression hyperlinkPageExpression;
	protected JRExpression hyperlinkTooltipExpression;
	
	protected String customizerClass;


	/**
	 * The bookmark level for the anchor associated with this chart.
	 * @see JRAnchor#getBookmarkLevel()
	 */
	protected int bookmarkLevel = JRAnchor.NO_BOOKMARK;

	/**
	 *
	 */
	public StandardChartSettings()
	{

	}
	
	
	public StandardChartSettings(ChartSettings chart, JRBaseObjectFactory factory)
	{
		showLegend = chart.getShowLegend();
		backcolor = chart.getBackcolor();
		linkType = chart.getLinkType();
		linkTarget = chart.getLinkTarget();
		titlePosition = chart.getTitlePosition();
		titleColor = chart.getTitleColor();
		subtitleColor = chart.getSubtitleColor();
		legendColor = chart.getLegendColor();
		legendBackgroundColor = chart.getLegendBackgroundColor();
		legendPosition = chart.getLegendPosition();
		renderType = chart.getRenderType();
		
		titleFont = chart.getTitleFont();
		subtitleFont = chart.getSubtitleFont();
		legendFont = chart.getLegendFont();
		titleExpression = factory.getExpression(chart.getTitleExpression());

		subtitleExpression = factory.getExpression(chart.getSubtitleExpression());
		anchorNameExpression = factory.getExpression(chart.getAnchorNameExpression());
		hyperlinkReferenceExpression = factory.getExpression(chart.getHyperlinkReferenceExpression());
		hyperlinkAnchorExpression = factory.getExpression(chart.getHyperlinkAnchorExpression());
		hyperlinkPageExpression = factory.getExpression(chart.getHyperlinkPageExpression());
		hyperlinkTooltipExpression = factory.getExpression(chart.getHyperlinkTooltipExpression());
		bookmarkLevel = chart.getBookmarkLevel();
		customizerClass = chart.getCustomizerClass();
		
		JRHyperlinkParameter[] hyperlinkParams = chart.getHyperlinkParameters();
		if (hyperlinkParams != null && hyperlinkParams.length > 0)
		{
			for(int i = 0; i < hyperlinkParams.length; i++)
			{
				addHyperlinkParameter(factory.getHyperlinkParameter(hyperlinkParams[i]));
			}
		}
	}
	/**
	 * 
	 */
	public Boolean getShowLegend()
	{
		return this.showLegend;
	}

	/**
	 *
	 */
	public void setShowLegend(Boolean isShowLegend)
	{
		Object old = this.showLegend;
		this.showLegend = isShowLegend;
		getEventSupport().firePropertyChange(PROPERTY_SHOW_LEGEND, old, this.showLegend);
	}

	/**
	 *
	 */
	public Color getBackcolor()
	{
		return this.backcolor;
	}

	/**
	 *
	 */
	public void setBackcolor(Color backcolor)
	{
		Object old = this.backcolor;
		this.backcolor = backcolor;
		getEventSupport().firePropertyChange(PROPERTY_BACKCOLOR, old, this.backcolor);
	}

	/**
	 *
	 */
	public JRFont getTitleFont()
	{
		return this.titleFont;
	}

	/**
	 *
	 */
	public EdgeEnum getTitlePosition()
	{
		return this.titlePosition;
	}

	/**
	 *
	 */
	public void setTitlePosition(EdgeEnum titlePosition)
	{
		Object old = this.titlePosition;
		this.titlePosition = titlePosition;
		getEventSupport().firePropertyChange(PROPERTY_TITLE_POSITION, old, this.titlePosition);
	}

	/**
	 *
	 */
	public Color getTitleColor()
	{
		return this.titleColor;
	}

	/**
	 *
	 */
	public void setTitleColor(Color titleColor)
	{
		Object old = this.titleColor;
		this.titleColor = titleColor;
		getEventSupport().firePropertyChange(PROPERTY_TITLE_COLOR, old, this.titleColor);
	}

	/**
	 *
	 */
	public JRFont getSubtitleFont()
	{
		return this.subtitleFont;
	}

	/**
	 *
	 */
	public Color getSubtitleColor()
	{
		return this.subtitleColor;
	}

	/**
	 *
	 */
	public void setSubtitleColor(Color subtitleColor)
	{
		Object old = this.subtitleColor;
		this.subtitleColor = subtitleColor;
		getEventSupport().firePropertyChange(PROPERTY_SUBTITLE_COLOR, old, this.subtitleColor);
	}

	/**
	 *
	 */
	public Color getLegendBackgroundColor() {
		return this.legendBackgroundColor;
	}

	/**
	 *
	 */
	public Color getLegendColor() {
		return this.legendColor;
	}

	/**
	 *
	 */
	public JRFont getLegendFont() {
		return this.legendFont;
	}

	/**
	 *
	 */
	public void setLegendBackgroundColor(Color legendBackgroundColor) {
		Object old = this.legendBackgroundColor;
		this.legendBackgroundColor = legendBackgroundColor;
		getEventSupport().firePropertyChange(PROPERTY_LEGEND_BACKGROUND_COLOR, old, this.legendBackgroundColor);
	}

	/**
	 *
	 */
	public void setLegendColor(Color legendColor) {
		Object old = this.legendColor;
		this.legendColor = legendColor;
		getEventSupport().firePropertyChange(PROPERTY_LEGEND_COLOR, old, this.legendColor);
	}

	/**
	 *
	 */
	public EdgeEnum getLegendPosition()
	{
		return this.legendPosition;
	}

	/**
	 *
	 */
	public void setLegendPosition(EdgeEnum legendPosition)
	{
		Object old = this.legendPosition;
		this.legendPosition = legendPosition;
		getEventSupport().firePropertyChange(PROPERTY_LEGEND_POSITION, old, this.legendPosition);
	}

	/**
	 * @deprecated Replaced by {@link #getHyperlinkTypeValue()}.
	 */
	public byte getHyperlinkType()
	{
		return getHyperlinkTypeValue().getValue();
	}
		
	/**
	 *
	 */
	public HyperlinkTypeEnum getHyperlinkTypeValue()
	{
		return JRHyperlinkHelper.getHyperlinkTypeValue(this);
	}
		
	/**
	 *
	 */
	public byte getHyperlinkTarget()
	{
		return JRHyperlinkHelper.getHyperlinkTarget(this);
	}
		
	/**
	 *
	 */
	public JRExpression getTitleExpression()
	{
		return this.titleExpression;
	}

	/**
	 *
	 */
	public JRExpression getSubtitleExpression()
	{
		return this.subtitleExpression;
	}

	/**
	 *
	 */
	public JRExpression getAnchorNameExpression()
	{
		return this.anchorNameExpression;
	}

	/**
	 *
	 */
	public JRExpression getHyperlinkReferenceExpression()
	{
		return this.hyperlinkReferenceExpression;
	}

	/**
	 *
	 */
	public JRExpression getHyperlinkAnchorExpression()
	{
		return this.hyperlinkAnchorExpression;
	}

	/**
	 *
	 */
	public JRExpression getHyperlinkPageExpression()
	{
		return this.hyperlinkPageExpression;
	}
	
	public byte getChartType()
	{
		return this.chartType;
	}

	/**
	 *
	 */
	public String getRenderType()
	{
		return this.renderType;
	}

	/**
	 *
	 */
	public void setRenderType(String renderType)
	{
		Object old = this.renderType;
		this.renderType = renderType;
		getEventSupport().firePropertyChange(PROPERTY_RENDER_TYPE, old, this.renderType);
	}

	public int getBookmarkLevel()
	{
		return this.bookmarkLevel;
	}

	/**
	 *
	 */
	public void setBookmarkLevel(int bookmarkLevel)
	{
		Object old = this.bookmarkLevel;
		this.bookmarkLevel = bookmarkLevel;
		getEventSupport().firePropertyChange(PROPERTY_BOOKMARK_LEVEL, old, this.bookmarkLevel);
	}


	public String getLinkType()
	{
		return this.linkType;
	}
	
	public String getLinkTarget()
	{
		return this.linkTarget;
	}

	public JRExpression getHyperlinkTooltipExpression()
	{
		return this.hyperlinkTooltipExpression;
	}


	/**
	 * @return the customizerClass
	 */
	public String getCustomizerClass()
	{
		return this.customizerClass;
	}


	/**
	 * @param chartType the chartType to set
	 */
	public void setChartType(Byte chartType) {
		Object old = this.chartType;
		this.chartType = chartType;
		getEventSupport().firePropertyChange(PROPERTY_CHART_TYPE, old, this.chartType);
	}
	
	/**
	 * @param linkType the linkType to set
	 */
	public void setLinkType(String linkType) {
		Object old = this.linkType;
		this.linkType = linkType;
		getEventSupport().firePropertyChange(PROPERTY_LINK_TYPE, old, this.linkType);
	}
	/**
	 * @param linkTarget the linkTarget to set
	 */
	public void setLinkTarget(String linkTarget) {
		Object old = this.linkTarget;
		this.linkTarget = linkTarget;
		getEventSupport().firePropertyChange(PROPERTY_LINK_TARGET, old, this.linkTarget);
	}

	/**
	 * @param titleFont the titleFont to set
	 */
	public void setTitleFont(JRFont titleFont) {
		Object old = this.linkTarget;
		this.titleFont = titleFont;
		getEventSupport().firePropertyChange(PROPERTY_TITLE_FONT, old, this.titleFont);
	}
	/**
	 * @param subtitleFont the subtitleFont to set
	 */
	public void setSubtitleFont(JRFont subtitleFont) {
		Object old = this.subtitleFont;
		this.subtitleFont = subtitleFont;
		getEventSupport().firePropertyChange(PROPERTY_SUBTITLE_FONT, old, this.subtitleFont);
	}
	/**
	 * @param legendFont the legendFont to set
	 */
	public void setLegendFont(JRFont legendFont) {
		Object old = this.legendFont;
		this.legendFont = legendFont;
		getEventSupport().firePropertyChange(PROPERTY_LEGEND_FONT, old, this.legendFont);
	}

	/**
	 * @param titleExpression the titleExpression to set
	 */
	public void setTitleExpression(JRExpression titleExpression) {
		Object old = this.titleExpression;
		this.titleExpression = titleExpression;
		getEventSupport().firePropertyChange(PROPERTY_TITLE_EXPRESSION, old, this.titleExpression);
	}


	/**
	 * @param subtitleExpression the subtitleExpression to set
	 */
	public void setSubtitleExpression(JRExpression subtitleExpression) {
		Object old = this.subtitleExpression;
		this.subtitleExpression = subtitleExpression;
		getEventSupport().firePropertyChange(PROPERTY_SUBTITLE_EXPRESSION, old, this.subtitleExpression);
	}


	/**
	 * @param anchorNameExpression the anchorNameExpression to set
	 */
	public void setAnchorNameExpression(JRExpression anchorNameExpression) {
		Object old = this.anchorNameExpression;
		this.anchorNameExpression = anchorNameExpression;
		getEventSupport().firePropertyChange(PROPERTY_ANCHOR_NAME_EXPRESSION, old, this.anchorNameExpression);
	}


	/**
	 * @param hyperlinkReferenceExpression the hyperlinkReferenceExpression to set
	 */
	public void setHyperlinkReferenceExpression(JRExpression hyperlinkReferenceExpression) {
		Object old = this.hyperlinkReferenceExpression;
		this.hyperlinkReferenceExpression = hyperlinkReferenceExpression;
		getEventSupport().firePropertyChange(PROPERTY_HYPERLINK_REFERENCE_EXPRESSION, old, this.hyperlinkReferenceExpression);
	}


	/**
	 * @param hyperlinkAnchorExpression the hyperlinkAnchorExpression to set
	 */
	public void setHyperlinkAnchorExpression(JRExpression hyperlinkAnchorExpression) {
		Object old = this.hyperlinkAnchorExpression;
		this.hyperlinkAnchorExpression = hyperlinkAnchorExpression;
		getEventSupport().firePropertyChange(PROPERTY_HYPERLINK_ANCHOR_EXPRESSION, old, this.hyperlinkAnchorExpression);
	}


	/**
	 * @param hyperlinkPageExpression the hyperlinkPageExpression to set
	 */
	public void setHyperlinkPageExpression(JRExpression hyperlinkPageExpression) {
		Object old = this.hyperlinkPageExpression;
		this.hyperlinkPageExpression = hyperlinkPageExpression;
		getEventSupport().firePropertyChange(PROPERTY_HYPERLINK_PAGE_EXPRESSION, old, this.hyperlinkPageExpression);
	}


	/**
	 * @param hyperlinkTooltipExpression the hyperlinkTooltipExpression to set
	 */
	public void setHyperlinkTooltipExpression(JRExpression hyperlinkTooltipExpression) {
		Object old = this.hyperlinkTooltipExpression;
		this.hyperlinkTooltipExpression = hyperlinkTooltipExpression;
		getEventSupport().firePropertyChange(PROPERTY_HYPERLINK_TOOLTIP_EXPRESSION, old, this.hyperlinkTooltipExpression);
	}

	/**
	 * @param customizerClass the customizerClass to set
	 */
	public void setCustomizerClass(String customizerClass) 
	{
		Object old = this.customizerClass;
		this.customizerClass = customizerClass;
		getEventSupport().firePropertyChange(PROPERTY_CUSTOMIZER_CLASS, old, this.customizerClass);
		
	}
	
	public void collectExpressions(JRExpressionCollector collector)
	{
		SpiderChartCompiler.collectExpressions(this, collector);
	}

	public Object clone() 
	{
		StandardChartSettings clone = null;
		try
		{
			clone = (StandardChartSettings)super.clone();
		}
		catch (CloneNotSupportedException e)
		{
			throw new JRRuntimeException(e);
		}
		clone.hyperlinkParameters = JRCloneUtils.cloneList(hyperlinkParameters);
		clone.titleExpression = JRCloneUtils.nullSafeClone(titleExpression);
		clone.subtitleExpression = JRCloneUtils.nullSafeClone(subtitleExpression);
		clone.anchorNameExpression = JRCloneUtils.nullSafeClone(anchorNameExpression);
		clone.hyperlinkReferenceExpression = JRCloneUtils.nullSafeClone(hyperlinkReferenceExpression);
		clone.hyperlinkAnchorExpression = JRCloneUtils.nullSafeClone(hyperlinkAnchorExpression);
		clone.hyperlinkPageExpression = JRCloneUtils.nullSafeClone(hyperlinkPageExpression);
		clone.hyperlinkTooltipExpression = JRCloneUtils.nullSafeClone(hyperlinkTooltipExpression);
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

	public JRHyperlinkParameter[] getHyperlinkParameters()
	{
		JRHyperlinkParameter[] parameters = null;
		if (!hyperlinkParameters.isEmpty())
		{
			parameters = new JRHyperlinkParameter[hyperlinkParameters.size()];
			hyperlinkParameters.toArray(parameters);
		}
		return parameters;
	}
	
	
	/**
	 * Returns the list of custom hyperlink parameters.
	 * 
	 * @return the list of custom hyperlink parameters
	 */
	public List<JRHyperlinkParameter> getHyperlinkParametersList()
	{
		return hyperlinkParameters;
	}
	
	
	/**
	 * Adds a custom hyperlink parameter.
	 * 
	 * @param parameter the parameter to add
	 */
	public void addHyperlinkParameter(JRHyperlinkParameter parameter)
	{
		hyperlinkParameters.add(parameter);
		getEventSupport().fireCollectionElementAddedEvent(JRDesignHyperlink.PROPERTY_HYPERLINK_PARAMETERS, 
				parameter, hyperlinkParameters.size() - 1);
	}
	

	/**
	 * Removes a custom hyperlink parameter.
	 * 
	 * @param parameter the parameter to remove
	 */
	public void removeHyperlinkParameter(JRHyperlinkParameter parameter)
	{
		int idx = hyperlinkParameters.indexOf(parameter);
		if (idx >= 0)
		{
			hyperlinkParameters.remove(idx);
			getEventSupport().fireCollectionElementRemovedEvent(JRDesignHyperlink.PROPERTY_HYPERLINK_PARAMETERS, 
					parameter, idx);
		}
	}
	
	
	/**
	 * Removes a custom hyperlink parameter.
	 * <p>
	 * If multiple parameters having the specified name exist, all of them
	 * will be removed
	 * </p>
	 * 
	 * @param parameterName the parameter name
	 */
	public void removeHyperlinkParameter(String parameterName)
	{
		for (ListIterator<JRHyperlinkParameter> it = hyperlinkParameters.listIterator(); it.hasNext();)
		{
			JRHyperlinkParameter parameter = it.next();
			if (parameter.getName() != null && parameter.getName().equals(parameterName))
			{
				it.remove();
				getEventSupport().fireCollectionElementRemovedEvent(JRDesignHyperlink.PROPERTY_HYPERLINK_PARAMETERS, 
						parameter, it.nextIndex());
			}
		}
	}

}
