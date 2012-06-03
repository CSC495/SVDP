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

import java.awt.Color;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import net.sf.jasperreports.crosstabs.JRCellContents;
import net.sf.jasperreports.crosstabs.fill.JRFillCrosstabObjectFactory;
import net.sf.jasperreports.crosstabs.type.CrosstabColumnPositionEnum;
import net.sf.jasperreports.crosstabs.type.CrosstabRowPositionEnum;
import net.sf.jasperreports.engine.JRDefaultStyleProvider;
import net.sf.jasperreports.engine.JRElement;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRFrame;
import net.sf.jasperreports.engine.JRLineBox;
import net.sf.jasperreports.engine.JROrigin;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.JRStyleSetter;
import net.sf.jasperreports.engine.type.ModeEnum;

import org.apache.commons.collections.ReferenceMap;

/**
 * Crosstab cell contents filler.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRFillCellContents.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class JRFillCellContents extends JRFillElementContainer implements JRCellContents, JRStyleSetter
{
	private final Map<StretchedContents,JRFillCellContents> transformedContentsCache;
	private final Map<BoxContents,JRFillCellContents> boxContentsCache;
	private final JRClonePool clonePool;
	private final JROriginProvider originProvider;
	private final int elementId;
	
	private JRFillCellContents original;
	
	private final JRCellContents parentCell;
	private final String cellType;
	
	private JRLineBox lineBox;
	
	private int height;
	private int width;
	
	private int x;
	private int y;
	private int verticalSpan;
	private CrosstabRowPositionEnum verticalPositionType = CrosstabRowPositionEnum.TOP;
	private int horizontalSpan;
	
	private Map<JRStyle,JRTemplateFrame> templateFrames;
	
	private JRDefaultStyleProvider defaultStyleProvider;
	private JRStyle initStyle;
	private int prepareStretchHeight;

	public JRFillCellContents(JRBaseFiller filler, JRCellContents cell, String cellType, 
			JRFillCrosstabObjectFactory factory)
	{
		super(filler, cell, factory);
		
		defaultStyleProvider = factory.getDefaultStyleProvider();
		
		parentCell = cell;
		this.cellType = cellType;
		elementId = filler.getFillContext().generateFillElementId();
		
		lineBox = cell.getLineBox().clone(this);
		
		width = cell.getWidth();
		height = cell.getHeight();
		
		factory.registerDelayedStyleSetter(this, parentCell);
		
		initElements();
		
		initConditionalStyles();
		
		initTemplatesMap();
		
		this.originProvider = factory.getParentOriginProvider();
		setElementOriginProvider(this.originProvider);
		
		transformedContentsCache = new ReferenceMap();
		boxContentsCache = new HashMap<BoxContents,JRFillCellContents>();
		clonePool = new JRClonePool(this, true, true);
	}

	private void initTemplatesMap()
	{
		templateFrames = new HashMap<JRStyle,JRTemplateFrame>();
	}

	protected JRFillCellContents(JRFillCellContents cellContents, JRFillCloneFactory factory)
	{
		super(cellContents, factory);
		
		defaultStyleProvider = cellContents.defaultStyleProvider;
		
		parentCell = cellContents.parentCell;
		cellType = cellContents.cellType;
		elementId = cellContents.elementId;
		
		lineBox = cellContents.getLineBox().clone(this);
		
		width = cellContents.width;
		height = cellContents.height;
		
		initStyle = cellContents.initStyle;
		
		initElements();
		
		initConditionalStyles();
		
		this.templateFrames = cellContents.templateFrames;
		
		this.originProvider = cellContents.originProvider;
		
		transformedContentsCache = new ReferenceMap();
		boxContentsCache = new HashMap<BoxContents,JRFillCellContents>();
		clonePool = new JRClonePool(this, true, true);
		
		verticalPositionType = cellContents.verticalPositionType;
	}

	public Color getBackcolor()
	{
		return parentCell.getBackcolor();
	}

	/**
	 *
	 */
	public JRLineBox getLineBox()
	{
		return lineBox;
	}

	/**
	 * 
	 */
	protected void setBox(JRLineBox box)
	{
		this.lineBox = box;
		
		initTemplatesMap();
	}
	
	public int getHeight()
	{
		return height;
	}
	
	
	public int getWidth()
	{
		return width;
	}
	
	
	protected void setHeight(int height)
	{
		this.height = height;
	}
	
	
	protected void setWidth(int width)
	{
		this.width = width;
	}
	
	
	public JRFillCellContents getBoxContents(boolean left, boolean right, boolean top)
	{
		if (lineBox == null)
		{
			return this;
		}
		
		boolean copyLeft = left && lineBox.getLeftPen().getLineWidth().floatValue() <= 0f && lineBox.getRightPen().getLineWidth().floatValue() > 0f;
		boolean copyRight = right && lineBox.getRightPen().getLineWidth().floatValue() <= 0f && lineBox.getLeftPen().getLineWidth().floatValue() > 0f;
		boolean copyTop = top && lineBox.getTopPen().getLineWidth().floatValue() <= 0f && lineBox.getBottomPen().getLineWidth().floatValue() > 0f;
		
		if (!(copyLeft || copyRight || copyTop))
		{
			return this;
		}
		
		Object key = new BoxContents(copyLeft, copyRight, copyTop);
		JRFillCellContents boxContents = boxContentsCache.get(key);
		if (boxContents == null)
		{
			boxContents = (JRFillCellContents) createClone();
			
			JRLineBox newBox = lineBox.clone(this);
			
			if (copyLeft)
			{
				newBox.copyLeftPen(lineBox.getRightPen());
			}
			
			if (copyRight)
			{
				newBox.copyRightPen(lineBox.getLeftPen());
			}
			
			if (copyTop)
			{
				newBox.copyTopPen(lineBox.getBottomPen());
			}
			
			boxContents.setBox(newBox);
			
			boxContentsCache.put((BoxContents)key, boxContents);
		}
		
		return boxContents;
	}
	
	
	public JRFillCellContents getTransformedContents(
			int newWidth, int newHeight,
			CrosstabColumnPositionEnum xPosition, CrosstabRowPositionEnum yPosition) throws JRException
	{
		if ((getHeight() == newHeight) && 
				(getWidth() == newWidth))
		{
			return this;
		}
		
		if (newHeight < getHeight() || newWidth < getWidth())
		{
			throw new JRException("Cannot shrink cell contents.");
		}
		
		Object key = new StretchedContents(newWidth, newHeight, xPosition, yPosition);
		
		JRFillCellContents transformedCell = transformedContentsCache.get(key);
		if (transformedCell == null)
		{
			transformedCell = (JRFillCellContents) createClone();
			transformedCell.transform(newWidth, newHeight, xPosition, yPosition);
			
			transformedContentsCache.put((StretchedContents)key, transformedCell);
		}
		
		return transformedCell;
	}
	
	
	private void transform(int newWidth, int newHeight, CrosstabColumnPositionEnum xPosition, CrosstabRowPositionEnum yPosition)
	{
		transformElements(newWidth, newHeight, xPosition, yPosition);
		
		width = newWidth;
		height = newHeight;
	}

	private void transformElements(int newWidth, int newHeight, CrosstabColumnPositionEnum xPosition, CrosstabRowPositionEnum yPosition)
	{
		if ((height == newHeight || yPosition == CrosstabRowPositionEnum.TOP) && 
				(width == newWidth || xPosition == CrosstabColumnPositionEnum.LEFT))
		{
			return;
		}

		double scaleX =  -1d;
		int offsetX = 0;
		switch (xPosition)
		{
			case CENTER:
				offsetX = (newWidth - width) / 2;
				break;
			case RIGHT:
				offsetX = newWidth - width;
				break;
			case STRETCH:
				scaleX = ((double) newWidth) / width;
				break;
		}
		
		double scaleY =  -1d;
		int offsetY = 0;
		switch (yPosition)
		{
			case MIDDLE:
				offsetY = (newHeight - height) / 2;
				break;
			case BOTTOM:
				offsetY = newHeight - height;
				break;
			case STRETCH:
				scaleY = ((double) newHeight) / height;
				break;
		}
		
		transformElements(getElements(), scaleX, offsetX, scaleY, offsetY);
	}

	private static void transformElements(JRElement[] elements, double scaleX, int offsetX, double scaleY, int offsetY)
	{
		if (elements != null)
		{
			for (int i = 0; i < elements.length; i++)
			{
				JRFillElement element = (JRFillElement) elements[i];
				
				if (scaleX != -1d)
				{
					element.setX((int) (element.getX() * scaleX));
					element.setWidth((int) (element.getWidth() * scaleX));
				}
				
				if (offsetX != 0)
				{
					element.setX(element.getX() + offsetX);
				}				
				
				if (scaleY != -1d)
				{
					element.setY((int) (element.getY() * scaleY));
					element.setHeight((int) (element.getHeight() * scaleY));
				}
				
				if (offsetY != 0)
				{
					element.setY(element.getY() + offsetY);
				}
				
				if (element instanceof JRFrame)
				{
					JRElement[] frameElements = ((JRFrame) element).getElements();
					transformElements(frameElements, scaleX, offsetX, scaleY, offsetY);
				}
			}
		}
	}
	
	
	protected void prepare(int availableHeight) throws JRException
	{
		initFill();
		resetElements();
		
		prepareElements(availableHeight, true);
		
		// store the original stretch height in order to compute the external
		// stretch height
		prepareStretchHeight = getStretchHeight();
	}

	
	protected JRPrintFrame fill() throws JRException
	{
		stretchElements();
		moveBandBottomElements();
		removeBlankElements();

		JRTemplatePrintFrame printCell = new JRTemplatePrintFrame(getTemplateFrame(), elementId);
		printCell.setX(x);
		printCell.setY(y);
		printCell.setWidth(width);
		
		fillElements(printCell);
		
		verticallyPositionElements(printCell);
		
		printCell.setHeight(getPrintHeight());
		
		setCellProperties(printCell);

		return printCell;
	}

	protected void setCellProperties(JRTemplatePrintFrame printCell)
	{
		if (verticalSpan > 1)
		{
			printCell.getPropertiesMap().setProperty(
					JRCellContents.PROPERTY_ROW_SPAN, Integer.toString(verticalSpan));
		}
		
		if (horizontalSpan > 1)
		{
			printCell.getPropertiesMap().setProperty(
					JRCellContents.PROPERTY_COLUMN_SPAN, Integer.toString(horizontalSpan));
		}
	}

	
	protected JRTemplateFrame getTemplateFrame()
	{
		JRStyle style = getStyle();
		JRTemplateFrame template = templateFrames.get(style);
		if (template == null)
		{
			template = new JRTemplateFrame(getOrigin(), 
					filler.getJasperPrint().getDefaultStyleProvider(), this);
			template.getPropertiesMap().setProperty(
					JRCellContents.PROPERTY_TYPE, cellType);
			
			template = filler.fillContext.deduplicate(template);
			templateFrames.put(style, template);
		}
		return template;
	}

	protected JROrigin getOrigin()
	{
		return originProvider == null ? null : originProvider.getOrigin();
	}
	
	protected void verticallyPositionElements(JRTemplatePrintFrame printCell)
	{
		int positionOffset;
		
		switch (verticalPositionType)
		{
			case MIDDLE:
				positionOffset = (getStretchHeight() - prepareStretchHeight) / 2;
				break;
			case BOTTOM:
				positionOffset = getStretchHeight() - prepareStretchHeight;
				break;
			default:
				positionOffset = 0;
				break;
		}
		
		if (positionOffset != 0)
		{
			List<JRPrintElement> printElements = printCell.getElements();
			
			int positionY = getStretchHeight() - positionOffset;
			boolean outside = false;
			for (Iterator<JRPrintElement> it = printElements.iterator(); !outside && it.hasNext();)
			{
				JRPrintElement element = it.next();
				outside = element.getY() + element.getHeight() > positionY;
			}
			
			if (!outside)
			{
				for (Iterator<JRPrintElement> it = printElements.iterator(); it.hasNext();)
				{
					JRPrintElement element = it.next();
					element.setY(element.getY() + positionOffset);
				}
			}
		}
	}

	protected int getPrintHeight()
	{
		return getStretchHeight() + getTopPadding() + getBottomPadding();
	}

	protected void stretchTo(int stretchHeight)
	{
		setStretchHeight(stretchHeight - getTopPadding() - getBottomPadding());
	}
	
	protected static class BoxContents
	{
		final boolean left;
		final boolean right;
		final boolean top;
		final int hashCode;
		
		public BoxContents(boolean left, boolean right, boolean top)
		{
			this.left = left;
			this.right = right;
			this.top = top;
			
			int hash = left ? 1231 : 1237;
			hash = 31*hash + (right ? 1231 : 1237);
			hash = 31*hash + (top ? 1231 : 1237);
			hashCode = hash;
		}

		public boolean equals(Object obj)
		{
			if (obj == this)
			{
				return true;
			}
			
			BoxContents b = (BoxContents) obj;
			
			return  
				b.left == left && b.right == right && b.top == top;
		}

		public int hashCode()
		{
			return hashCode;
		}
	}
	
	protected static class StretchedContents
	{
		final int newHeight;
		final int newWidth;
		final int hashCode;
		final CrosstabColumnPositionEnum xPosition;
		final CrosstabRowPositionEnum yPosition;
		
		StretchedContents(
				int newWidth, int newHeight, CrosstabColumnPositionEnum xPosition, CrosstabRowPositionEnum yPosition)
		{
			this.newHeight = newHeight;
			this.newWidth = newWidth;
			this.xPosition = xPosition;
			this.yPosition = yPosition;
			
			int hash = newHeight;
			hash = 31*hash + newWidth;
			hash = 31*hash + xPosition.getValue();
			hash = 31*hash + yPosition.getValue();
			hashCode = hash;
		}
		
		public boolean equals(Object o)
		{
			if (o == this)
			{
				return true;
			}
			
			StretchedContents s = (StretchedContents) o;
			
			return  
				s.newHeight == newHeight && s.newWidth == newWidth &&
				s.xPosition == xPosition && s.yPosition == yPosition;
		}
		
		public int hashCode()
		{
			return hashCode;
		}
	}

	protected int getContainerHeight()
	{
		return getHeight() - getTopPadding() - getBottomPadding();
	}
	
	protected int getTopPadding()
	{
		return lineBox == null ? 0 : lineBox.getTopPadding().intValue();
	}
	
	protected int getBottomPadding()
	{
		return lineBox == null ? 0 : lineBox.getBottomPadding().intValue();
	}

	public JRFillCloneable createClone()
	{
		JRFillCloneFactory factory = new JRFillCloneFactory();
		return createClone(factory);
	}

	public JRFillCloneable createClone(JRFillCloneFactory factory)
	{
		return new JRFillCellContents(this, factory);
	}
	
	public JRFillCellContents getWorkingClone()
	{
		JRFillCellContents clone = (JRFillCellContents) clonePool.getClone();
		clone.original = this;
		return clone;
	}
	
	public void releaseWorkingClone()
	{
		original.clonePool.releaseClone(this);
	}

	public void setX(int x)
	{
		this.x = x;
	}

	public void setY(int y)
	{
		this.y = y;
	}

	public int getVerticalSpan()
	{
		return verticalSpan;
	}

	public void setVerticalSpan(int span)
	{
		verticalSpan = span;
	}

	public void setVerticalPositionType(CrosstabRowPositionEnum positionType)
	{
		this.verticalPositionType = positionType;
	}
	
	public int getHorizontalSpan()
	{
		return horizontalSpan;
	}

	public void setHorizontalSpan(int horizontalSpan)
	{
		this.horizontalSpan = horizontalSpan;
	}


	protected void evaluate(byte evaluation) throws JRException
	{
		evaluateConditionalStyles(evaluation);
		super.evaluate(evaluation);
	}

	public JRDefaultStyleProvider getDefaultStyleProvider()
	{
		return defaultStyleProvider;
	}

	public JRStyle getStyle()
	{
		JRStyle crtStyle = initStyle;
		
		boolean isUsingDefaultStyle = false;

		if (crtStyle == null)
		{
			crtStyle = filler.getDefaultStyle();
			isUsingDefaultStyle = true;
		}

		JRStyle evalStyle = getEvaluatedConditionalStyle(crtStyle);
		
		if (isUsingDefaultStyle && evalStyle == crtStyle)
		{
			evalStyle = null;
		}
		return evalStyle;
	}

	protected void initConditionalStyles()
	{
		super.initConditionalStyles();
		collectConditionalStyle(initStyle);
	}

	public ModeEnum getModeValue()
	{
		return parentCell.getModeValue();
	}

	public String getStyleNameReference()
	{
		return null;
	}

	public void setStyle(JRStyle style)
	{
		this.initStyle = style;
		collectConditionalStyle(style);
	}

	public void setStyleNameReference(String name)
	{
		throw new UnsupportedOperationException("Style name references not allowed at fill time");
	}

	/**
	 * 
	 */
	public Color getDefaultLineColor() 
	{
		return parentCell.getDefaultLineColor();
	}

}
