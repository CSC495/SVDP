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

/*
 * Contributors:
 * Eugene D - eugenedruy@users.sourceforge.net 
 * Adrian Jackson - iapetus@users.sourceforge.net
 * David Taylor - exodussystems@users.sourceforge.net
 * Lars Kristensen - llk@users.sourceforge.net
 */
package net.sf.jasperreports.engine.convert;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import net.sf.jasperreports.crosstabs.JRCellContents;
import net.sf.jasperreports.crosstabs.JRCrosstab;
import net.sf.jasperreports.crosstabs.JRCrosstabCell;
import net.sf.jasperreports.crosstabs.JRCrosstabColumnGroup;
import net.sf.jasperreports.crosstabs.JRCrosstabRowGroup;
import net.sf.jasperreports.crosstabs.type.CrosstabTotalPositionEnum;
import net.sf.jasperreports.engine.JRChild;
import net.sf.jasperreports.engine.JRElement;
import net.sf.jasperreports.engine.JRLineBox;
import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.base.JRBaseLineBox;
import net.sf.jasperreports.engine.base.JRBasePrintFrame;
import net.sf.jasperreports.engine.type.RunDirectionEnum;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: CrosstabConverter.java 4844 2011-12-07 13:03:27Z lucianc $
 */
public final class CrosstabConverter extends FrameConverter
{

	/**
	 *
	 */
	private final static CrosstabConverter INSTANCE = new CrosstabConverter();
	
	/**
	 *
	 */
	private CrosstabConverter()
	{
	}

	/**
	 *
	 */
	public static ElementConverter getInstance()
	{
		return INSTANCE;
	}
	
	/**
	 *
	 */
	public JRPrintElement convert(ReportConverter reportConverter, JRElement element)
	{
		JRBasePrintFrame printFrame = new JRBasePrintFrame(reportConverter.getDefaultStyleProvider());
		JRCrosstab crosstab = (JRCrosstab)element; 
		
		printFrame.copyBox(crosstab.getLineBox());
		copyElement(reportConverter, crosstab, printFrame);
		
		List<JRPrintElement> children = getCrosstabChildren(reportConverter, crosstab);
		if (children != null && children.size() > 0)
		{
//			ConvertVisitor convertVisitor = new ConvertVisitor(reportConverter, printFrame);
			for(int i = 0; i < children.size(); i++)
			{
//				((JRChild)children.get(i)).visit(convertVisitor);
				printFrame.addElement(children.get(i));
			}
		}
		
		return printFrame;
	}

	/**
	 *
	 */
	private List<JRPrintElement> getCrosstabChildren(ReportConverter reportConverter, JRCrosstab crosstab)
	{
		List<JRPrintElement> crosstabElements = new ArrayList<JRPrintElement>();
		
		JRCrosstabRowGroup[] rowGroups = crosstab.getRowGroups();
		int rowHeadersXOffset = 0;
		for (int i = 0; i < rowGroups.length; i++)
		{
			rowHeadersXOffset += rowGroups[i].getWidth();
		}
		
		JRCrosstabColumnGroup[] columnGroups = crosstab.getColumnGroups();
		int colHeadersYOffset = 0;
		for (int i = 0; i < columnGroups.length; i++)
		{
			colHeadersYOffset += columnGroups[i].getHeight();
		}
		
		JRCellContents headerCell = crosstab.getHeaderCell();
		if (headerCell != null)
		{
			if (headerCell.getWidth() != 0 && headerCell.getHeight() != 0)
			{
				crosstabElements.add(
					getCrosstabCellFrame(
						reportConverter,
						headerCell, 
						0, 
						0, 
						false, 
						false, 
						false
						));

			}
			
		}
		
		addCrosstabColumnHeaders(
			reportConverter,
			crosstab, 
			rowHeadersXOffset, 
			crosstabElements
			);
		addCrosstabRows(
			reportConverter,
			crosstab, 
			rowHeadersXOffset, 
			colHeadersYOffset, 
			crosstabElements
			);
		
		if (crosstab.getRunDirectionValue() == RunDirectionEnum.RTL)
		{
			mirrorElements(crosstabElements, crosstab.getX(), crosstab.getWidth());
		}
		
		return crosstabElements;
	}
	
	/**
	 *
	 */
	private void mirrorElements(List<JRPrintElement> elements, int x, int width)
	{
		for (Iterator<JRPrintElement> it = elements.iterator(); it.hasNext();)
		{
			JRPrintElement element = it.next();
			int mirrorX = width - element.getX() - element.getWidth();
			element.setX(mirrorX);
		}
	}
	
	/**
	 * 
	 */
	private JRPrintFrame getCrosstabCellFrame(
		ReportConverter reportConverter,
		JRCellContents cell, 
		int x, 
		int y, 
		boolean left, 
		boolean right, 
		boolean top
		)
	{
		JRBasePrintFrame frame = new JRBasePrintFrame(cell.getDefaultStyleProvider());
		frame.setX(x);
		frame.setY(y);
		frame.setWidth(cell.getWidth());
		frame.setHeight(cell.getHeight());
		
		frame.setMode(cell.getModeValue());
		frame.setBackcolor(cell.getBackcolor());
		frame.setStyle(reportConverter.resolveStyle(cell));
		
		JRLineBox box = cell.getLineBox();
		if (box != null)
		{
			frame.copyBox(box);
			
			boolean copyLeft = left && box.getLeftPen().getLineWidth().floatValue() <= 0f && box.getRightPen().getLineWidth().floatValue() > 0f;
			boolean copyRight = right && box.getRightPen().getLineWidth().floatValue() <= 0f && box.getLeftPen().getLineWidth().floatValue() > 0f;
			boolean copyTop = top && box.getTopPen().getLineWidth().floatValue() <= 0f && box.getBottomPen().getLineWidth().floatValue() > 0f;
			
			if (copyLeft)
			{
				((JRBaseLineBox)frame.getLineBox()).copyLeftPen(box.getRightPen());
			}
			
			if (copyRight)
			{
				((JRBaseLineBox)frame.getLineBox()).copyRightPen(box.getLeftPen());
			}
			
			if (copyTop)
			{
				((JRBaseLineBox)frame.getLineBox()).copyTopPen(box.getBottomPen());
			}
		}
		
//		List children = cell.getChildren();
//		if (children != null)
//		{
//			for (Iterator it = children.iterator(); it.hasNext();)
//			{
//				JRChild child = (JRChild) it.next();
//				if (child instanceof JRElement)
//				{
//					frame.addElement((JRElement) child);
//				}
//				else if (child instanceof JRElementGroup)
//				{
//					frame.addElementGroup((JRElementGroup) child);
//				}
//			}
//		}
		List<JRChild> children = cell.getChildren();
		if (children != null && children.size() > 0)
		{
			ConvertVisitor convertVisitor = new ConvertVisitor(reportConverter, frame);
			for(int i = 0; i < children.size(); i++)
			{
				children.get(i).visit(convertVisitor);
			}
		}
		
		return frame;
	}
	
	/**
	 * 
	 */
	private void addCrosstabColumnHeaders(
		ReportConverter reportConverter,
		JRCrosstab crosstab, 
		int rowHeadersXOffset, 
		List<JRPrintElement> crosstabElements
		)
	{
		JRCrosstabColumnGroup[] groups = crosstab.getColumnGroups();
		
		for (int i = 0, x = 0, y = 0; i < groups.length; i++)
		{
			JRCrosstabColumnGroup group = groups[i];
			
			if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.START)
			{
				JRCellContents totalHeader = group.getTotalHeader();
				if (totalHeader.getWidth() != 0 && totalHeader.getHeight() != 0)
				{
					boolean firstOnRow = x == 0 && crosstab.getHeaderCell() == null;
					crosstabElements.add(
						getCrosstabCellFrame(
							reportConverter,
							totalHeader, 
							rowHeadersXOffset + x, 
							y, 
							firstOnRow && crosstab.getRunDirectionValue() != RunDirectionEnum.RTL, //LTR or null
							firstOnRow && crosstab.getRunDirectionValue() == RunDirectionEnum.RTL,
							false
							));
	
					x += totalHeader.getWidth();
				}
			}
			
			JRCellContents header = group.getHeader();
			if (header.getWidth() != 0 && header.getHeight() != 0) {
				boolean firstOnRow = x == 0 && crosstab.getHeaderCell() == null;
				crosstabElements.add(
					getCrosstabCellFrame(
						reportConverter,
						header, 
						rowHeadersXOffset + x, 
						y, 
						firstOnRow && crosstab.getRunDirectionValue() != RunDirectionEnum.RTL, //LTR or null
						firstOnRow && crosstab.getRunDirectionValue() == RunDirectionEnum.RTL,
						false
						));
			}
			
			if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.END)
			{
				JRCellContents totalHeader = group.getTotalHeader();
				if (totalHeader.getWidth() != 0 && totalHeader.getHeight() != 0)
				{
					crosstabElements.add(
						getCrosstabCellFrame(
							reportConverter,
							totalHeader, 
							rowHeadersXOffset + x + header.getWidth(), 
							y, 
							false, 
							false, 
							false
							));
				}
				
			}
			
			y += group.getHeight();
		}
	}

	/**
	 * 
	 */
	private void addCrosstabRows(
		ReportConverter reportConverter,
		JRCrosstab crosstab, 
		int rowHeadersXOffset, 
		int colHeadersYOffset, 
		List<JRPrintElement> crosstabElements
		)
	{
		JRCrosstabRowGroup[] groups = crosstab.getRowGroups();
		
		for (int i = 0, x = 0, y = 0; i < groups.length; i++)
		{
			JRCrosstabRowGroup group = groups[i];
			
			if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.START)
			{
				JRCellContents totalHeader = group.getTotalHeader();
				if (totalHeader.getWidth() != 0 && totalHeader.getHeight() != 0)
				{
					crosstabElements.add(
						getCrosstabCellFrame(
							reportConverter,
							totalHeader, 
							x, 
							colHeadersYOffset + y, 
							false, 
							false, 
							y == 0 && crosstab.getHeaderCell() == null
							));
					
					addCrosstabDataCellsRow(
						reportConverter,
						crosstab, 
						rowHeadersXOffset, 
						colHeadersYOffset + y, 
						i, 
						crosstabElements
						);
					y += totalHeader.getHeight();
				}
			}
			
			JRCellContents header = group.getHeader();
			if (header.getWidth() != 0 && header.getHeight() != 0)
			{
				crosstabElements.add(
					getCrosstabCellFrame(
						reportConverter,
						header, 
						x, 
						colHeadersYOffset + y, 
						false, 
						false, 
						y == 0 && crosstab.getHeaderCell() == null
						));
			}
			
			if (i == groups.length - 1)
			{
				addCrosstabDataCellsRow(
					reportConverter,
					crosstab, 
					rowHeadersXOffset, 
					colHeadersYOffset + y, 
					groups.length, 
					crosstabElements
					);				
			}
			
			if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.END)
			{
				JRCellContents totalHeader = group.getTotalHeader();
				if (totalHeader.getWidth() != 0 && totalHeader.getHeight() != 0)
				{
					crosstabElements.add(
						getCrosstabCellFrame(
							reportConverter,
							totalHeader, 
							x, 
							colHeadersYOffset + y + header.getHeight(), 
							false, 
							false, 
							false
							));
					
					addCrosstabDataCellsRow(
						reportConverter,
						crosstab, 
						rowHeadersXOffset, 
						colHeadersYOffset + y + header.getHeight(), 
						i, 
						crosstabElements
						);
				}
			}
			
			x += group.getWidth();
		}
	}

	/**
	 * 
	 */
	private void addCrosstabDataCellsRow(
		ReportConverter reportConverter,
		JRCrosstab crosstab, 
		int rowOffsetX, 
		int rowOffsetY, 
		int rowIndex,
		List<JRPrintElement> crosstabElements
		)
	{
		JRCrosstabCell[][] cells = crosstab.getCells();
		if (cells != null)
		{
			JRCrosstabColumnGroup[] colGroups = crosstab.getColumnGroups();
			int crosstabX = rowOffsetX;
			int crosstabY = rowOffsetY;

			for (int i = 0, x = 0; i < colGroups.length; i++)
			{
				JRCrosstabColumnGroup group = colGroups[i];
				
				if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.START)
				{
					JRCrosstabCell cell = cells[rowIndex][i];
					if (cell != null)
					{
						JRCellContents contents = cell.getContents();
						if (contents.getWidth() != 0 && contents.getHeight() != 0)
						{
							crosstabElements.add(
								getCrosstabCellFrame(
									reportConverter,
									contents, 
									crosstabX + x, 
									crosstabY, 
									false, 
									false, 
									false
									));
							x += cells[rowIndex][i].getContents().getWidth();
						}
					}
				}
				
				if (i == colGroups.length - 1)
				{
					JRCrosstabCell cell = cells[rowIndex][colGroups.length];
					if (cell != null)
					{
						JRCellContents contents = cell.getContents();
						if (contents.getWidth() != 0 && contents.getHeight() != 0)
						{
							crosstabElements.add(
								getCrosstabCellFrame(
									reportConverter,
									contents, 
									crosstabX + x, 
									crosstabY, 
									false, 
									false, 
									false
									));
						}
					}
				}
				
				if (group.getTotalPositionValue() == CrosstabTotalPositionEnum.END)
				{
					JRCrosstabCell cell = cells[rowIndex][i];
					if (cell != null)
					{
						JRCellContents contents = cell.getContents();
						if (contents.getWidth() != 0 && contents.getHeight() != 0)
						{
							crosstabElements.add(
								getCrosstabCellFrame(
									reportConverter,
									contents, 
									crosstabX + x + group.getHeader().getWidth(), 
									crosstabY, 
									false, 
									false, 
									false
									));
						}
					}
				}
			}
		}
	}

}
