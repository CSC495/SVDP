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
package net.sf.jasperreports.engine.export.oasis;

import java.io.IOException;
import java.io.Writer;

import net.sf.jasperreports.engine.JRParagraph;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.TabStop;
import net.sf.jasperreports.engine.export.LengthUtil;
import net.sf.jasperreports.engine.type.HorizontalAlignEnum;
import net.sf.jasperreports.engine.type.RotationEnum;
import net.sf.jasperreports.engine.type.RunDirectionEnum;
import net.sf.jasperreports.engine.type.TabStopAlignEnum;
import net.sf.jasperreports.engine.type.VerticalAlignEnum;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ParagraphStyle.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class ParagraphStyle extends Style
{
	/**
	 *
	 */
	protected static final String HORIZONTAL_ALIGN_LEFT = "start";
	protected static final String HORIZONTAL_ALIGN_RIGHT = "end";
	protected static final String HORIZONTAL_ALIGN_CENTER = "center";
	protected static final String HORIZONTAL_ALIGN_JUSTIFY = "justified";

	/**
	 *
	 */
	private static final String VERTICAL_ALIGN_TOP = "top";
	private static final String VERTICAL_ALIGN_MIDDLE = "middle";
	private static final String VERTICAL_ALIGN_BOTTOM = "bottom";

	/**
	 *
	 */
	protected static final String TAB_STOP_ALIGN_LEFT = "left";
	protected static final String TAB_STOP_ALIGN_RIGHT = "right";
	protected static final String TAB_STOP_ALIGN_CENTER = "center";

	/**
	*
	*/
	protected static final String ROTATION_ALIGN_NONE = "none";
	protected static final String ROTATION_ALIGN_TOP = "top";
	protected static final String ROTATION_ALIGN_CENTER = "center";
	protected static final String ROTATION_ALIGN_BOTTOM = "bottom";

	private String verticalAlignment;
	private String horizontalAlignment;
	private String runDirection;
	private String textRotation = "0";
	private JRParagraph paragraph;

	/**
	 *
	 */
	public ParagraphStyle(Writer styleWriter, JRPrintText text)
	{
		super(styleWriter);
		
		horizontalAlignment = getHorizontalAlignment(text.getHorizontalAlignmentValue(), text.getVerticalAlignmentValue(), text.getRotationValue());
		verticalAlignment = getVerticalAlignment(text.getHorizontalAlignmentValue(), text.getVerticalAlignmentValue(), text.getRotationValue());
		
		switch(text.getRotationValue())
		{
			case LEFT:
			{
				textRotation = "90";
				break;
			}
			case RIGHT:
			{
				textRotation = "270";
				break;
			}
			case UPSIDE_DOWN://FIXMEODT possible?
			case NONE:
			default:
			{
				textRotation = "0";
			}
		}

		runDirection = null;
		if (text.getRunDirectionValue() == RunDirectionEnum.RTL)
		{
			runDirection = "rl";
		}
		
		paragraph = text.getParagraph();
	}
	
	/**
	 *
	 */
	public static String getVerticalAlignment(
		HorizontalAlignEnum horizontalAlignment, 
		VerticalAlignEnum verticalAlignment, 
		RotationEnum rotation
		)
	{
		switch(rotation)
		{
			case LEFT:
			{
				switch (horizontalAlignment)
				{
					case RIGHT :
						return VERTICAL_ALIGN_TOP;
					case CENTER :
						return VERTICAL_ALIGN_MIDDLE;
					case JUSTIFIED :
						return HORIZONTAL_ALIGN_JUSTIFY;//FIXMEODT ?????????????????
					case LEFT :
					default :
						return VERTICAL_ALIGN_BOTTOM;
				}
			}
			case RIGHT:
			{
				switch (horizontalAlignment)
				{
					case RIGHT :
						return VERTICAL_ALIGN_BOTTOM;
					case CENTER :
						return VERTICAL_ALIGN_MIDDLE;
					case JUSTIFIED :
						return HORIZONTAL_ALIGN_JUSTIFY;//?????????????????
					case LEFT :
					default :
						return VERTICAL_ALIGN_TOP;
				}
			}
			case UPSIDE_DOWN://FIXMEODT possible?
			case NONE:
			default:
			{
				switch (verticalAlignment)
				{
					case BOTTOM :
						return VERTICAL_ALIGN_BOTTOM;
					case MIDDLE :
						return VERTICAL_ALIGN_MIDDLE;
					case TOP :
					default :
						return VERTICAL_ALIGN_TOP;
				}
			}
		}
	}
	
	/**
	 *
	 */
	public static String getHorizontalAlignment(
		HorizontalAlignEnum horizontalAlignment, 
		VerticalAlignEnum verticalAlignment, 
		RotationEnum rotation
		)
	{
		switch(rotation)
		{
			case LEFT:
			{
				switch (verticalAlignment)
				{
					case BOTTOM :
						return HORIZONTAL_ALIGN_RIGHT;
					case MIDDLE :
						return HORIZONTAL_ALIGN_CENTER;
					case TOP :
					default :
						return HORIZONTAL_ALIGN_LEFT;
				}
			}
			case RIGHT:
			{
				switch (verticalAlignment)
				{
					case BOTTOM :
						return HORIZONTAL_ALIGN_LEFT;
					case MIDDLE :
						return HORIZONTAL_ALIGN_CENTER;
					case TOP :
					default :
						return HORIZONTAL_ALIGN_RIGHT;
				}
			}
			case UPSIDE_DOWN://FIXMEODT possible?
			case NONE:
			default:
			{
				switch (horizontalAlignment)
				{
					case RIGHT :
						return HORIZONTAL_ALIGN_RIGHT;
					case CENTER :
						return HORIZONTAL_ALIGN_CENTER;
					case JUSTIFIED :
						return HORIZONTAL_ALIGN_JUSTIFY;
					case LEFT :
					default :
						return HORIZONTAL_ALIGN_LEFT;
				}
			}
		}
	}
	
	/**
	 *
	 */
	public static String getTabStopAlignment(TabStopAlignEnum tabStopAlignment)
	{
		switch (tabStopAlignment)
		{
			case RIGHT :
				return TAB_STOP_ALIGN_RIGHT;
			case CENTER :
				return TAB_STOP_ALIGN_CENTER;
			case LEFT :
			default :
				return TAB_STOP_ALIGN_LEFT;
		}
	}
	
	/**
	 *
	 */
	public String getId()
	{
		StringBuffer sbuffer = new StringBuffer();
		sbuffer.append(verticalAlignment).append("|").append(horizontalAlignment)
			.append("|").append(runDirection).append("|").append(textRotation)
			.append("|").append(paragraph.getLineSpacing().getName())
			.append("|").append(paragraph.getLineSpacingSize())
			.append("|").append(paragraph.getFirstLineIndent())
			.append("|").append(paragraph.getLeftIndent())
			.append("|").append(paragraph.getRightIndent())
			.append("|").append(paragraph.getSpacingBefore())
			.append("|").append(paragraph.getSpacingAfter()); // + "|" + tabStopWidth;// tabStopWidth can only be set in default-style
		TabStop[] tabStops = paragraph.getTabStops();
		if (tabStops != null && tabStops.length > 0)
		{
			for (int i = 0; i < tabStops.length; i++)
			{
				TabStop tabStop = tabStops[i];
				sbuffer.append("|").append(tabStop.getPosition()).append("|").append(tabStop.getAlignment().getName());
			}
		}
		
		return sbuffer.toString();
	}

	/**
	 *
	 */
	public void write(String paragraphStyleName) throws IOException
	{
		styleWriter.write("<style:style style:name=\"" + paragraphStyleName + "\"");
		styleWriter.write(" style:family=\"paragraph\">\n");
		styleWriter.write("<style:paragraph-properties");
		switch (paragraph.getLineSpacing())
		{
			case SINGLE:
			default:
			{
				styleWriter.write(" fo:line-height=\"100%\"");
				break;
			}
			case ONE_AND_HALF:
			{
				styleWriter.write(" fo:line-height=\"150%\"");
				break;
			}
			case DOUBLE:
			{
				styleWriter.write(" fo:line-height=\"200%\"");
				break;
			}
			case AT_LEAST:
			{
				styleWriter.write(" style:line-height-at-least=\"" + LengthUtil.inch(paragraph.getLineSpacingSize()) + "in\"");
				break;
			}
			case FIXED:
			{
				styleWriter.write(" fo:line-height=\"" + LengthUtil.inch(paragraph.getLineSpacingSize()) + "in\"");
				break;
			}
			case PROPORTIONAL:
			{
				styleWriter.write(" fo:line-height=\"" + (100 * paragraph.getLineSpacingSize()) + "%\"");
				break;
			}
		}
//		styleWriter.write(" fo:line-height=\"" + pLineHeight + "\"");
//		styleWriter.write(" style:line-spacing=\"" + pLineSpacing + "\"");
		styleWriter.write(" fo:text-align=\"" + horizontalAlignment + "\"");

//		styleWriter.write(" fo:keep-together=\"" + pKeepTogether + "\"");
		styleWriter.write(" fo:text-indent=\"" + LengthUtil.inch(paragraph.getFirstLineIndent()) + "in\"");
		styleWriter.write(" fo:margin-left=\"" + LengthUtil.inch(paragraph.getLeftIndent()) + "in\"");
		styleWriter.write(" fo:margin-right=\"" + LengthUtil.inch(paragraph.getRightIndent()) + "in\"");
		styleWriter.write(" fo:margin-top=\"" + LengthUtil.inch(paragraph.getSpacingBefore()) + "in\"");
		styleWriter.write(" fo:margin-bottom=\"" + LengthUtil.inch(paragraph.getSpacingAfter()) + "in\"");
//		styleWriter.write(" fo:background-color=\"#" + pBackGroundColor + "\"");
		styleWriter.write(" style:vertical-align=\"" + verticalAlignment + "\"");
		if (runDirection != null)
		{
			styleWriter.write(" style:writing-mode=\"" + runDirection + "\"");
		}
		styleWriter.write(">\n");

		TabStop[] tabStops = paragraph.getTabStops();
		if (tabStops != null && tabStops.length > 0)
		{
			styleWriter.write("<style:tab-stops>");
			for (int i = 0; i < tabStops.length; i++)
			{
				TabStop tabStop = tabStops[i];
				styleWriter.write("<style:tab-stop style:type=\"" + getTabStopAlignment(tabStop.getAlignment()) + "\" style:position=\"" + LengthUtil.inch(tabStop.getPosition()) + "in\"/>");
			}
			styleWriter.write("</style:tab-stops>");
		}
		
		styleWriter.write("</style:paragraph-properties>\n");
		styleWriter.write("<style:text-properties");
		styleWriter.write(" style:text-rotation-angle=\"" + textRotation + "\"");
		styleWriter.write(">\n");
		styleWriter.write("</style:text-properties>\n");
		
//		styleWriter.write("<style:properties");
//		styleWriter.write(" style:rotation-align=\"" + rotationAlignment + "\"");
//		styleWriter.write(">\n");
//		styleWriter.write("</style:properties>\n");
//

		styleWriter.write("</style:style>\n");
	}

}

