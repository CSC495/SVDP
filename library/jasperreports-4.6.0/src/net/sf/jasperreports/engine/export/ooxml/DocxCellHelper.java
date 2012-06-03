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
package net.sf.jasperreports.engine.export.ooxml;

import java.awt.Color;
import java.io.Writer;

import net.sf.jasperreports.engine.JRAlignment;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.export.JRExporterGridCell;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.type.RotationEnum;
import net.sf.jasperreports.engine.type.VerticalAlignEnum;
import net.sf.jasperreports.engine.util.JRColorUtil;


/**
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: DocxCellHelper.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class DocxCellHelper extends BaseHelper
{
	/**
	 *
	 */
	private static final String VERTICAL_ALIGN_TOP = "top";
	private static final String VERTICAL_ALIGN_MIDDLE = "center";
	private static final String VERTICAL_ALIGN_BOTTOM = "bottom";
	
	/**
	 *
	 */
	private DocxBorderHelper borderHelper;
	
	/**
	 *
	 */
	public DocxCellHelper(Writer writer)
	{
		super(writer);
		
		borderHelper = new DocxBorderHelper(writer);
	}
		
	/**
	 * 
	 */
	public DocxBorderHelper getBorderHelper() 
	{
		return borderHelper;
	}

	/**
	 *
	 */
	public void exportHeader(JRPrintElement element, JRExporterGridCell gridCell) 
	{
		write("    <w:tc>\n");
		
		exportPropsHeader();

		if (gridCell.getColSpan() > 1)
		{
			write("      <w:gridSpan w:val=\"" + gridCell.getColSpan() +"\" />\n");
		}
		if (gridCell.getRowSpan() > 1)
		{
			write("      <w:vMerge w:val=\"restart\" />\n");
		}
		
		exportProps(element, gridCell);
		
		exportPropsFooter();
	}

	/**
	 *
	 */
	public void exportFooter() 
	{
		write("    </w:tc>\n");
	}


	/**
	 *
	 */
	public void exportProps(JRPrintElement element, JRExporterGridCell gridCell)
	{
		exportBackcolor(element.getModeValue(), element.getBackcolor());
		
		borderHelper.export(gridCell.getBox());

//		if (element instanceof JRCommonGraphicElement)
//			borderHelper.export(((JRCommonGraphicElement)element).getLinePen());
		
		JRAlignment align = element instanceof JRAlignment ? (JRAlignment)element : null;
		if (align != null)
		{
			JRPrintText text = element instanceof JRPrintText ? (JRPrintText)element : null;
			RotationEnum ownRotation = text == null ? null : text.getOwnRotationValue();
			
			String verticalAlignment = 
				getVerticalAlignment(
					align.getOwnVerticalAlignmentValue() 
					);
			String textRotation = getTextDirection(ownRotation);

			exportAlignmentAndRotation(verticalAlignment, textRotation);
		}
	}


	/**
	 *
	 */
	public void exportProps(JRExporterGridCell gridCell)
	{
		exportBackcolor(ModeEnum.OPAQUE, gridCell.getBackcolor());//FIXMEDOCX check this
		
		borderHelper.export(gridCell.getBox());
	}

	
	/**
	 *
	 */
	private void exportBackcolor(ModeEnum mode, Color backcolor)
	{
		if (mode == ModeEnum.OPAQUE && backcolor != null)
		{
			write("      <w:shd w:val=\"clear\" w:color=\"auto\"	w:fill=\"" + JRColorUtil.getColorHexa(backcolor) + "\" />\n");
		}
	}

	/**
	 *
	 */
	private void exportPropsHeader()
	{
		write("      <w:tcPr>\n");
	}
	
	/**
	 *
	 */
	private void exportAlignmentAndRotation(String verticalAlignment, String textRotation)
	{
		if (verticalAlignment != null)
		{
			write("      <w:vAlign w:val=\"" + verticalAlignment +"\" />\n");
		}
		if (textRotation != null)
		{
			write("   <w:textDirection w:val=\"" + textRotation + "\" />\n");
		}
	}
	
	/**
	 *
	 */
	private void exportPropsFooter()
	{
		write("      </w:tcPr>\n");
	}
	
	/**
	 *
	 */
	private static String getTextDirection(RotationEnum rotation)
	{
		String textDirection = null;
		
		if (rotation != null)
		{
			switch(rotation)
			{
				case LEFT:
				{
					textDirection = "btLr";
					break;
				}
				case RIGHT:
				{
					textDirection = "tbRl";
					break;
				}
				case UPSIDE_DOWN://FIXMEDOCX possible?
				case NONE:
				default:
				{
				}
			}
		}

		return textDirection;
	}

	/**
	 *
	 */
	public static String getVerticalAlignment(VerticalAlignEnum verticalAlignment)
	{
		if (verticalAlignment != null)
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
		return null;
	}
}
