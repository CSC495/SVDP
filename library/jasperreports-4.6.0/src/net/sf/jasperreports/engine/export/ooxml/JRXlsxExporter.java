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
import java.awt.Dimension;
import java.awt.geom.Dimension2D;
import java.io.IOException;
import java.io.OutputStream;
import java.io.Writer;
import java.text.AttributedCharacterIterator;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRGenericPrintElement;
import net.sf.jasperreports.engine.JRLineBox;
import net.sf.jasperreports.engine.JRPen;
import net.sf.jasperreports.engine.JRPrintElementIndex;
import net.sf.jasperreports.engine.JRPrintFrame;
import net.sf.jasperreports.engine.JRPrintGraphicElement;
import net.sf.jasperreports.engine.JRPrintHyperlink;
import net.sf.jasperreports.engine.JRPrintImage;
import net.sf.jasperreports.engine.JRPrintLine;
import net.sf.jasperreports.engine.JRPrintPage;
import net.sf.jasperreports.engine.JRPrintText;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRStyle;
import net.sf.jasperreports.engine.JRWrappingSvgRenderer;
import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.Renderable;
import net.sf.jasperreports.engine.RenderableUtil;
import net.sf.jasperreports.engine.base.JRBaseLineBox;
import net.sf.jasperreports.engine.export.Cut;
import net.sf.jasperreports.engine.export.ElementGridCell;
import net.sf.jasperreports.engine.export.ExporterNature;
import net.sf.jasperreports.engine.export.GenericElementHandlerEnviroment;
import net.sf.jasperreports.engine.export.JRExporterGridCell;
import net.sf.jasperreports.engine.export.JRGridLayout;
import net.sf.jasperreports.engine.export.JRHyperlinkProducer;
import net.sf.jasperreports.engine.export.JRXlsAbstractExporter;
import net.sf.jasperreports.engine.export.LengthUtil;
import net.sf.jasperreports.engine.export.OccupiedGridCell;
import net.sf.jasperreports.engine.export.XlsRowLevelInfo;
import net.sf.jasperreports.engine.export.data.BooleanTextValue;
import net.sf.jasperreports.engine.export.data.DateTextValue;
import net.sf.jasperreports.engine.export.data.NumberTextValue;
import net.sf.jasperreports.engine.export.data.StringTextValue;
import net.sf.jasperreports.engine.export.data.TextValue;
import net.sf.jasperreports.engine.export.data.TextValueHandler;
import net.sf.jasperreports.engine.export.zip.ExportZipEntry;
import net.sf.jasperreports.engine.export.zip.FileBufferedZipEntry;
import net.sf.jasperreports.engine.type.ImageTypeEnum;
import net.sf.jasperreports.engine.type.LineDirectionEnum;
import net.sf.jasperreports.engine.type.ModeEnum;
import net.sf.jasperreports.engine.type.RenderableTypeEnum;
import net.sf.jasperreports.engine.util.JRDataUtils;
import net.sf.jasperreports.engine.util.JRStyledText;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;


/**
 * Exports a JasperReports document to XLSX format. It has character output type and exports the document to a
 * grid-based layout.
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRXlsxExporter.java 5359 2012-05-09 16:07:51Z shertage $
 */
public class JRXlsxExporter extends JRXlsAbstractExporter
{
	private static final Log log = LogFactory.getLog(JRXlsxExporter.class);
	
	/**
	 * The exporter key, as used in
	 * {@link GenericElementHandlerEnviroment#getHandler(net.sf.jasperreports.engine.JRGenericElementType, String)}.
	 */
	public static final String XLSX_EXPORTER_KEY = JRPropertiesUtil.PROPERTY_PREFIX + "xlsx";

	protected static final String XLSX_EXPORTER_PROPERTIES_PREFIX = JRPropertiesUtil.PROPERTY_PREFIX + "export.xlsx.";

	/**
	 * Property used to store the location of an existing workbook template containing a macro object. 
	 * The macro object will be copied into the generated document if the template location is valid. 
	 * Macros can be loaded from Excel macro-enabled template files (*.xltm) as well as from valid 
	 * Excel macro-enabled documents (*.xlsm).
	 * 
	 * @see JRPropertiesUtil
	 * @since 4.5.1
	 */
	public static final String PROPERTY_MACRO_TEMPLATE = JRPropertiesUtil.PROPERTY_PREFIX + "export.xlsx.macro.template";
	/**
	 *
	 */
	protected static final String JR_PAGE_ANCHOR_PREFIX = "JR_PAGE_ANCHOR_";

	/**
	 *
	 */
	public static final String IMAGE_NAME_PREFIX = "img_";
	protected static final int IMAGE_NAME_PREFIX_LEGTH = IMAGE_NAME_PREFIX.length();

	/**
	 *
	 */
	protected XlsxZip xlsxZip;
	protected XlsxWorkbookHelper wbHelper;
	protected XlsxRelsHelper relsHelper;
	protected XlsxContentTypesHelper ctHelper;
	protected XlsxSheetHelper sheetHelper;
	protected XlsxSheetRelsHelper sheetRelsHelper;
	protected XlsxDrawingHelper drawingHelper;
	protected XlsxDrawingRelsHelper drawingRelsHelper;
	protected XlsxStyleHelper styleHelper;
	protected XlsxCellHelper cellHelper;//FIXMEXLSX maybe cell helper should be part of sheet helper, just like in table helper

	protected Map<String, String> rendererToImagePathMap;
//	protected Map imageMaps;
	protected List<JRPrintElementIndex> imagesToProcess;
//	protected Map hyperlinksMap;

	protected int tableIndex;
	protected boolean startPage;


	protected LinkedList<Color> backcolorStack = new LinkedList<Color>();
	protected Color backcolor;

	private XlsxRunHelper runHelper;

	protected ExporterNature nature;
	
	protected String sheetAutoFilter;		
	
	protected String macroTemplate;		
	
	protected JasperPrint currentSheetJasperPrint;	
	
	protected Integer currentSheetPageScale;	
	
	protected Integer currentSheetFirstPageNumber;		
	
	protected JRXlsxExporterContext exporterContext = new ExporterContext();

	
	protected class ExporterContext extends BaseExporterContext implements JRXlsxExporterContext
	{
		public String getExportPropertiesPrefix()
		{
			return XLSX_EXPORTER_PROPERTIES_PREFIX;
		}
	}

	
	/**
	 * @see #JRXlsxExporter(JasperReportsContext)
	 */
	public JRXlsxExporter()
	{
		this(DefaultJasperReportsContext.getInstance());
	}


	/**
	 *
	 */
	public JRXlsxExporter(JasperReportsContext jasperReportsContext)
	{
		super(jasperReportsContext);
	}


	/**
	 *
	 */
	protected void setParameters()
	{
		super.setParameters();

		nature = new JRXlsxExporterNature(jasperReportsContext, filter, isIgnoreGraphics, isIgnorePageMargins);

		macroTemplate =  macroTemplate == null ? getPropertiesUtil().getProperty(jasperPrint, PROPERTY_MACRO_TEMPLATE) : macroTemplate;
		
//		password = 
//			getStringParameter(
//				JExcelApiExporterParameter.PASSWORD,
//				JExcelApiExporterParameter.PROPERTY_PASSWORD
//				);
	}


	/**
	 *
	 */
	public JRPrintImage getImage(List<JasperPrint> jasperPrintList, String imageName) throws JRException
	{
		return getImage(jasperPrintList, getPrintElementIndex(imageName));
	}


	public JRPrintImage getImage(List<JasperPrint> jasperPrintList, JRPrintElementIndex imageIndex) throws JRException//FIXMECONTEXT move these to an abstract up?
	{
		JasperPrint report = jasperPrintList.get(imageIndex.getReportIndex());
		JRPrintPage page = report.getPages().get(imageIndex.getPageIndex());

		Integer[] elementIndexes = imageIndex.getAddressArray();
		Object element = page.getElements().get(elementIndexes[0].intValue());

		for (int i = 1; i < elementIndexes.length; ++i)
		{
			JRPrintFrame frame = (JRPrintFrame) element;
			element = frame.getElements().get(elementIndexes[i].intValue());
		}

		if(element instanceof JRGenericPrintElement)
		{
			JRGenericPrintElement genericPrintElement = (JRGenericPrintElement)element;
			return ((GenericElementXlsxHandler)GenericElementHandlerEnviroment.getInstance(jasperReportsContext).getElementHandler(
					genericPrintElement.getGenericType(), 
					XLSX_EXPORTER_KEY
					)).getImage(exporterContext, genericPrintElement);
		}
		
		return (JRPrintImage) element;
	}


	/**
	 *
	 */
	protected void exportStyledText(JRStyle style, JRStyledText styledText, Locale locale)
	{
		String text = styledText.getText();

		int runLimit = 0;

		AttributedCharacterIterator iterator = styledText.getAttributedString().getIterator();

		while(runLimit < styledText.length() && (runLimit = iterator.getRunLimit()) <= styledText.length())
		{
			runHelper.export(
				style, iterator.getAttributes(), 
				text.substring(iterator.getIndex(), runLimit),
				locale
				);

			iterator.setIndex(runLimit);
		}
	}


	/**
	 *
	 */
	protected String getImagePath(Renderable renderer, boolean isLazy, JRExporterGridCell gridCell)
	{
		String imagePath = null;

		if (renderer != null)
		{
			if (renderer.getTypeValue() == RenderableTypeEnum.IMAGE && rendererToImagePathMap.containsKey(renderer.getId()))
			{
				imagePath = rendererToImagePathMap.get(renderer.getId());
			}
			else
			{
//				if (isLazy)//FIXMEDOCX learn how to link images
//				{
//					imagePath = ((JRImageRenderer)renderer).getImageLocation();
//				}
//				else
//				{
					JRPrintElementIndex imageIndex = getElementIndex(gridCell);
					imagesToProcess.add(imageIndex);

					String mimeType = renderer.getImageTypeValue().getMimeType();//FIXMEPPTX this code for file extension is duplicated
					if (mimeType == null)
					{
						mimeType = ImageTypeEnum.JPEG.getMimeType();
					}
					String extension = mimeType.substring(mimeType.lastIndexOf('/') + 1);

					String imageName = IMAGE_NAME_PREFIX + imageIndex.toString() + "." + extension;
					imagePath = imageName;
					//imagePath = "Pictures/" + imageName;
//				}

				rendererToImagePathMap.put(renderer.getId(), imagePath);
			}
		}

		return imagePath;
	}


	protected JRPrintElementIndex getElementIndex(JRExporterGridCell gridCell)
	{
		JRPrintElementIndex imageIndex =
			new JRPrintElementIndex(
					reportIndex,
					pageIndex,
					gridCell.getWrapper().getAddress()
					);
		return imageIndex;
	}


	/**
	 *
	 *
	protected void writeImageMap(String imageMapName, JRPrintHyperlink mainHyperlink, List imageMapAreas) throws IOException
	{
		writer.write("<map name=\"" + imageMapName + "\">\n");

		for (Iterator it = imageMapAreas.iterator(); it.hasNext();)
		{
			JRPrintImageAreaHyperlink areaHyperlink = (JRPrintImageAreaHyperlink) it.next();
			JRPrintImageArea area = areaHyperlink.getArea();

			writer.write("  <area shape=\"" + JRPrintImageArea.getHtmlShape(area.getShape()) + "\"");
			writeImageAreaCoordinates(area);
			writeImageAreaHyperlink(areaHyperlink.getHyperlink());
			writer.write("/>\n");
		}

		if (mainHyperlink.getHyperlinkTypeValue() != NONE)
		{
			writer.write("  <area shape=\"default\"");
			writeImageAreaHyperlink(mainHyperlink);
			writer.write("/>\n");
		}

		writer.write("</map>\n");
	}


	protected void writeImageAreaCoordinates(JRPrintImageArea area) throws IOException
	{
		int[] coords = area.getCoordinates();
		if (coords != null && coords.length > 0)
		{
			StringBuffer coordsEnum = new StringBuffer(coords.length * 4);
			coordsEnum.append(coords[0]);
			for (int i = 1; i < coords.length; i++)
			{
				coordsEnum.append(',');
				coordsEnum.append(coords[i]);
			}

			writer.write(" coords=\"" + coordsEnum + "\"");
		}
	}


	protected void writeImageAreaHyperlink(JRPrintHyperlink hyperlink) throws IOException
	{
		String href = getHyperlinkURL(hyperlink);
		if (href == null)
		{
			writer.write(" nohref=\"nohref\"");
		}
		else
		{
			writer.write(" href=\"" + href + "\"");

			String target = getHyperlinkTarget(hyperlink);
			if (target != null)
			{
				writer.write(" target=\"");
				writer.write(target);
				writer.write("\"");
			}
		}

		if (hyperlink.getHyperlinkTooltip() != null)
		{
			writer.write(" title=\"");
			writer.write(JRStringUtil.xmlEncode(hyperlink.getHyperlinkTooltip()));
			writer.write("\"");
		}
	}


	/**
	 *
	 */
	public static JRPrintElementIndex getPrintElementIndex(String imageName)
	{
		if (!imageName.startsWith(IMAGE_NAME_PREFIX))
		{
			throw new JRRuntimeException("Invalid image name: " + imageName);
		}

		return JRPrintElementIndex.parsePrintElementIndex(imageName.substring(IMAGE_NAME_PREFIX_LEGTH));
	}


	/**
	 *
	 */
	protected void setBackcolor(Color color)
	{
		backcolorStack.addLast(backcolor);

		backcolor = color;
	}


	protected void restoreBackcolor()
	{
		backcolor = backcolorStack.removeLast();
	}


//	private float getXAlignFactor(JRPrintImage image)
//	{
//		float xalignFactor = 0f;
//		switch (image.getHorizontalAlignmentValue())
//		{
//			case RIGHT :
//			{
//				xalignFactor = 1f;
//				break;
//			}
//			case CENTER :
//			{
//				xalignFactor = 0.5f;
//				break;
//			}
//			case LEFT :
//			default :
//			{
//				xalignFactor = 0f;
//				break;
//			}
//		}
//		return xalignFactor;
//	}


//	private float getYAlignFactor(JRPrintImage image)
//	{
//		float yalignFactor = 0f;
//		switch (image.getVerticalAlignmentValue())
//		{
//			case BOTTOM :
//			{
//				yalignFactor = 1f;
//				break;
//			}
//			case MIDDLE :
//			{
//				yalignFactor = 0.5f;
//				break;
//			}
//			case TOP :
//			default :
//			{
//				yalignFactor = 0f;
//				break;
//			}
//		}
//		return yalignFactor;
//	}

//	protected boolean startHyperlink(JRPrintHyperlink link, boolean isText)
//	{
//		String href = getHyperlinkURL(link);
//
//		if (href != null)
//		{
//			String id = (String)hyperlinksMap.get(href);
//			if (id == null)
//			{
//				id = "rIdLnk" + hyperlinksMap.size();
//				hyperlinksMap.put(href, id);
//			}
////			
////			wbHelper.write("<w:hyperlink r:id=\"" + id + "\"");
////
////			String target = getHyperlinkTarget(link);//FIXMETARGET
////			if (target != null)
////			{
////				wbHelper.write(" tgtFrame=\"" + target + "\"");
////			}
////
////			wbHelper.write(">\n");
//
//			sheetRelsHelper.exportHyperlink(id, href);
//
////			String tooltip = link.getHyperlinkTooltip(); 
////			if (tooltip != null)
////			{
////				wbHelper.write(" \\o \"" + JRStringUtil.xmlEncode(tooltip) + "\"");
////			}
////
////			wbHelper.write(" </w:instrText></w:r>\n");
////			wbHelper.write("<w:r><w:fldChar w:fldCharType=\"separate\"/></w:r>\n");
//		}
//
//		return href != null;
//	}


	protected String getHyperlinkTarget(JRPrintHyperlink link)
	{
		String target = null;
		switch(link.getHyperlinkTargetValue())
		{
			case SELF :
			{
				target = "_self";
				break;
			}
			case BLANK :
			default :
			{
				target = "_blank";
				break;
			}
		}
		return target;
	}


	protected String getHyperlinkURL(JRPrintHyperlink link)
	{
		String href = null;
		JRHyperlinkProducer customHandler = getHyperlinkProducer(link);
		if (customHandler == null)
		{
			switch(link.getHyperlinkTypeValue())
			{
				case REFERENCE :
				{
					if (link.getHyperlinkReference() != null)
					{
						href = link.getHyperlinkReference();
					}
					break;
				}
				case LOCAL_ANCHOR :
				{
//					if (link.getHyperlinkAnchor() != null)
//					{
//						href = "#" + link.getHyperlinkAnchor();
//					}
					break;
				}
				case LOCAL_PAGE :
				{
//					if (link.getHyperlinkPage() != null)
//					{
//						href = "#" + JR_PAGE_ANCHOR_PREFIX + reportIndex + "_" + link.getHyperlinkPage().toString();
//					}
					break;
				}
				case REMOTE_ANCHOR :
				{
					if (
						link.getHyperlinkReference() != null &&
						link.getHyperlinkAnchor() != null
						)
					{
						href = link.getHyperlinkReference() + "#" + link.getHyperlinkAnchor();
					}
					break;
				}
				case REMOTE_PAGE :
				{
//					if (
//						link.getHyperlinkReference() != null &&
//						link.getHyperlinkPage() != null
//						)
//					{
//						href = link.getHyperlinkReference() + "#" + JR_PAGE_ANCHOR_PREFIX + "0_" + link.getHyperlinkPage().toString();
//					}
					break;
				}
				case NONE :
				default :
				{
					break;
				}
			}
		}
		else
		{
			href = customHandler.getHyperlink(link);
		}

		return href;
	}


//	protected void endHyperlink(boolean isText)
//	{
////		wbHelper.write("</w:hyperlink>\n");
//		wbHelper.write("<w:r><w:fldChar w:fldCharType=\"end\"/></w:r>\n");
//	}

//	protected void insertPageAnchor() throws IOException
//	{
//		if(startPage)
//		{
//			tempBodyWriter.write("<text:bookmark text:name=\"");
//			tempBodyWriter.write(JR_PAGE_ANCHOR_PREFIX + reportIndex + "_" + (pageIndex + 1));
//			tempBodyWriter.write("\"/>\n");
//			startPage = false;
//		}
//	}
	
	/**
	 *
	 */
	protected String getExporterPropertiesPrefix()
	{
		return XLSX_EXPORTER_PROPERTIES_PREFIX;
	}


	protected void addBlankCell(
		JRExporterGridCell gridCell, 
		int colIndex,
		int rowIndex
		) throws JRException 
	{
		cellHelper.exportHeader(gridCell, rowIndex, colIndex);
		cellHelper.exportFooter();
	}


	protected void closeWorkbook(OutputStream os) throws JRException 
	{
		closeSheet();
		
		styleHelper.export();
		
		styleHelper.close();

		try
		{
			wbHelper.exportFooter();

			wbHelper.close();

			if ((imagesToProcess != null && imagesToProcess.size() > 0))
			{
				for(Iterator<JRPrintElementIndex> it = imagesToProcess.iterator(); it.hasNext();)
				{
					JRPrintElementIndex imageIndex = it.next();

					JRPrintImage image = getImage(jasperPrintList, imageIndex);
					Renderable renderer = image.getRenderable();
					if (renderer.getTypeValue() == RenderableTypeEnum.SVG)
					{
						renderer =
							new JRWrappingSvgRenderer(
								renderer,
								new Dimension(image.getWidth(), image.getHeight()),
								ModeEnum.OPAQUE == image.getModeValue() ? image.getBackcolor() : null
								);
					}

					String mimeType = renderer.getImageTypeValue().getMimeType();
					if (mimeType == null)
					{
						mimeType = ImageTypeEnum.JPEG.getMimeType();
					}
					String extension = mimeType.substring(mimeType.lastIndexOf('/') + 1);
					
					String imageName = IMAGE_NAME_PREFIX + imageIndex.toString() + "." + extension;
					
					xlsxZip.addEntry(//FIXMEDOCX optimize with a different implementation of entry
						new FileBufferedZipEntry(
							"xl/media/" + imageName,
							renderer.getImageData(jasperReportsContext)
							)
						);
					
//					drawingRelsHelper.exportImage(imageName);
				}
			}

//			if ((hyperlinksMap != null && hyperlinksMap.size() > 0))
//			{
//				for(Iterator it = hyperlinksMap.keySet().iterator(); it.hasNext();)
//				{
//					String href = (String)it.next();
//					String id = (String)hyperlinksMap.get(href);
	//
//					relsHelper.exportHyperlink(id, href);
//				}
//			}

			relsHelper.exportFooter();

			relsHelper.close();
			
			ctHelper.exportFooter();
			
			ctHelper.close();

			xlsxZip.zipEntries(os);

			xlsxZip.dispose();
		}
		catch (IOException e)
		{
			throw new JRException(e);
		}
	}


	protected void createSheet(String name)
	{
		closeSheet();
		
		currentSheetJasperPrint = jasperPrint;
		currentSheetPageScale = sheetPageScale;
		currentSheetFirstPageNumber = sheetFirstPageNumber;
		
		wbHelper.exportSheet(sheetIndex + 1, name);
		ctHelper.exportSheet(sheetIndex + 1);
		relsHelper.exportSheet(sheetIndex + 1);

		ExportZipEntry sheetRelsEntry = xlsxZip.addSheetRels(sheetIndex + 1);
		Writer sheetRelsWriter = sheetRelsEntry.getWriter();
		sheetRelsHelper = new XlsxSheetRelsHelper(sheetRelsWriter);

		ExportZipEntry sheetEntry = xlsxZip.addSheet(sheetIndex + 1);
		Writer sheetWriter = sheetEntry.getWriter();
		sheetHelper = 
			new XlsxSheetHelper(
				jasperReportsContext,
				sheetWriter, 
				sheetRelsHelper,
				isCollapseRowSpan
				);
		
		ExportZipEntry drawingRelsEntry = xlsxZip.addDrawingRels(sheetIndex + 1);
		Writer drawingRelsWriter = drawingRelsEntry.getWriter();
		drawingRelsHelper = new XlsxDrawingRelsHelper(drawingRelsWriter);
		
		ExportZipEntry drawingEntry = xlsxZip.addDrawing(sheetIndex + 1);
		Writer drawingWriter = drawingEntry.getWriter();
		drawingHelper = new XlsxDrawingHelper(drawingWriter, drawingRelsHelper);
		
		cellHelper = new XlsxCellHelper(sheetWriter, styleHelper);
		
		runHelper = new XlsxRunHelper(sheetWriter, fontMap, null);//FIXMEXLSX check this null
		
		sheetHelper.exportHeader(sheetPageScale == null ? 0 : sheetPageScale, gridRowFreezeIndex, gridColumnFreezeIndex, jasperPrint);
		sheetRelsHelper.exportHeader(sheetIndex + 1);
		drawingHelper.exportHeader();
		drawingRelsHelper.exportHeader();
	}


	protected void closeSheet()
	{
		if (sheetHelper != null)
		{
		
			
			if(currentSheetFirstPageNumber != null && currentSheetFirstPageNumber > 0)
			{
				sheetHelper.exportFooter(
						sheetIndex, 
						currentSheetJasperPrint == null ? jasperPrint : currentSheetJasperPrint, 
						isIgnorePageMargins, 
						sheetAutoFilter,
						currentSheetPageScale, 
						currentSheetFirstPageNumber,
						false
						);
					firstPageNotSet = false;
			}
			else if(documentFirstPageNumber != null && documentFirstPageNumber > 0 && firstPageNotSet)
			{
				sheetHelper.exportFooter(
						sheetIndex, 
						currentSheetJasperPrint == null ? jasperPrint : currentSheetJasperPrint, 
						isIgnorePageMargins, 
						sheetAutoFilter,
						currentSheetPageScale, 
						documentFirstPageNumber,
						false
						);
					firstPageNotSet = false;
			}
			else
			{
				sheetHelper.exportFooter(
						sheetIndex, 
						currentSheetJasperPrint == null ? jasperPrint : currentSheetJasperPrint, 
						isIgnorePageMargins, 
						sheetAutoFilter,
						currentSheetPageScale, 
						null,
						firstPageNotSet
						);
			}
			sheetHelper.close();

			sheetRelsHelper.exportFooter();
			sheetRelsHelper.close();
			
			drawingHelper.exportFooter();
			drawingHelper.close();

			drawingRelsHelper.exportFooter();
			drawingRelsHelper.close();
		}
	}


	protected void exportFrame(
		JRPrintFrame frame, 
		JRExporterGridCell gridCell,
		int colIndex, 
		int rowIndex
		) throws JRException 
	{
		cellHelper.exportHeader(gridCell, rowIndex, colIndex);
		sheetHelper.exportMergedCells(rowIndex, colIndex, gridCell.getRowSpan(), gridCell.getColSpan());

//		boolean appendBackcolor =
//			frame.getModeValue() == ModeEnum.OPAQUE
//			&& (backcolor == null || frame.getBackcolor().getRGB() != backcolor.getRGB());
//
//		if (appendBackcolor)
//		{
//			setBackcolor(frame.getBackcolor());
//		}
//
//		try
//		{
//			JRGridLayout layout = gridCell.getLayout();
//			JRPrintElementIndex frameIndex =
//				new JRPrintElementIndex(
//						reportIndex,
//						pageIndex,
//						gridCell.getWrapper().getAddress()
//						);
//			exportGrid(layout, frameIndex);
//		}
//		finally
//		{
//			if (appendBackcolor)
//			{
//				restoreBackcolor();
//			}
//		}
		
		cellHelper.exportFooter();
	}


	public void exportImage(
		JRPrintImage image, 
		JRExporterGridCell gridCell,
		int colIndex, 
		int rowIndex, 
		int emptyCols,
		int yCutsRow,
		JRGridLayout layout
		) throws JRException 
	{
		int topPadding =
			Math.max(image.getLineBox().getTopPadding().intValue(), getImageBorderCorrection(image.getLineBox().getTopPen()));
		int leftPadding =
			Math.max(image.getLineBox().getLeftPadding().intValue(), getImageBorderCorrection(image.getLineBox().getLeftPen()));
		int bottomPadding =
			Math.max(image.getLineBox().getBottomPadding().intValue(), getImageBorderCorrection(image.getLineBox().getBottomPen()));
		int rightPadding =
			Math.max(image.getLineBox().getRightPadding().intValue(), getImageBorderCorrection(image.getLineBox().getRightPen()));

		int availableImageWidth = image.getWidth() - leftPadding - rightPadding;
		availableImageWidth = availableImageWidth < 0 ? 0 : availableImageWidth;

		int availableImageHeight = image.getHeight() - topPadding - bottomPadding;
		availableImageHeight = availableImageHeight < 0 ? 0 : availableImageHeight;

		cellHelper.exportHeader(gridCell, rowIndex, colIndex);

		Renderable renderer = image.getRenderable();

		if (
			renderer != null &&
			availableImageWidth > 0 &&
			availableImageHeight > 0
			)
		{
			if (renderer.getTypeValue() == RenderableTypeEnum.IMAGE)
			{
				// Non-lazy image renderers are all asked for their image data at some point.
				// Better to test and replace the renderer now, in case of lazy load error.
				renderer = RenderableUtil.getInstance(jasperReportsContext).getOnErrorRendererForImageData(renderer, image.getOnErrorTypeValue());
			}
		}
		else
		{
			renderer = null;
		}

		if (renderer != null)
		{
			int width = availableImageWidth;
			int height = availableImageHeight;

			double normalWidth = availableImageWidth;
			double normalHeight = availableImageHeight;

			// Image load might fail.
			Renderable tmpRenderer =
				RenderableUtil.getInstance(jasperReportsContext).getOnErrorRendererForDimension(renderer, image.getOnErrorTypeValue());
			Dimension2D dimension = tmpRenderer == null ? null : tmpRenderer.getDimension(jasperReportsContext);
			// If renderer was replaced, ignore image dimension.
			if (tmpRenderer == renderer && dimension != null)
			{
				normalWidth = dimension.getWidth();
				normalHeight = dimension.getHeight();
			}

			double cropTop = 0;
			double cropLeft = 0;
			double cropBottom = 0;
			double cropRight = 0;
			
			switch (image.getScaleImageValue())
			{
				case FILL_FRAME :
				{
					width = availableImageWidth;
					height = availableImageHeight;
 					break;
				}
				case CLIP :
				{
//					if (normalWidth > availableImageWidth)
//					{
						switch (image.getHorizontalAlignmentValue())
						{
							case RIGHT :
							{
								cropLeft = 100000 * (availableImageWidth - normalWidth) / availableImageWidth;
								cropRight = 0;
								break;
							}
							case CENTER :
							{
								cropLeft = 100000 * (availableImageWidth - normalWidth) / availableImageWidth / 2;
								cropRight = cropLeft;
								break;
							}
							case LEFT :
							default :
							{
								cropLeft = 0;
								cropRight = 100000 * (availableImageWidth - normalWidth) / availableImageWidth;
								break;
							}
						}
//					}
//					else
//					{
//						width = (int)normalWidth;
//					}

//					if (normalHeight > availableImageHeight)
//					{
						switch (image.getVerticalAlignmentValue())
						{
							case TOP :
							{
								cropTop = 0;
								cropBottom = 100000 * (availableImageHeight - normalHeight) / availableImageHeight;
								break;
							}
							case MIDDLE :
							{
								cropTop = 100000 * (availableImageHeight - normalHeight) / availableImageHeight / 2;
								cropBottom = cropTop;
								break;
							}
							case BOTTOM :
							default :
							{
								cropTop = 100000 * (availableImageHeight - normalHeight) / availableImageHeight;
								cropBottom = 0;
								break;
							}
						}
//					}
//					else
//					{
//						height = (int)normalHeight;
//					}

					break;
				}
				case RETAIN_SHAPE :
				default :
				{
					if (availableImageHeight > 0)//FIXMEXLSX this is useless. test is above. check all
					{
						double ratio = normalWidth / normalHeight;

						if( ratio > availableImageWidth / (double)availableImageHeight )
						{
							width = availableImageWidth;
							height = (int)(width/ratio);

							switch (image.getVerticalAlignmentValue())
							{
								case TOP :
								{
									cropTop = 0;
									cropBottom = 100000 * (availableImageHeight - height) / availableImageHeight;
									break;
								}
								case MIDDLE :
								{
									cropTop = 100000 * (availableImageHeight - height) / availableImageHeight / 2;
									cropBottom = cropTop;
									break;
								}
								case BOTTOM :
								default :
								{
									cropTop = 100000 * (availableImageHeight - height) / availableImageHeight;
									cropBottom = 0;
									break;
								}
							}
						}
						else
						{
							height = availableImageHeight;
							width = (int)(ratio * height);

							switch (image.getHorizontalAlignmentValue())
							{
								case RIGHT :
								{
									cropLeft = 100000 * (availableImageWidth - width) / availableImageWidth;
									cropRight = 0;
									break;
								}
								case CENTER :
								{
									cropLeft = 100000 * (availableImageWidth - width) / availableImageWidth / 2;
									cropRight = cropLeft;
									break;
								}
								case LEFT :
								default :
								{
									cropLeft = 0;
									cropRight = 100000 * (availableImageWidth - width) / availableImageWidth;
									break;
								}
							}
						}
					}
				}
			}

//			insertPageAnchor();
//			if (image.getAnchorName() != null)
//			{
//				tempBodyWriter.write("<text:bookmark text:name=\"");
//				tempBodyWriter.write(image.getAnchorName());
//				tempBodyWriter.write("\"/>");
//			}

//			boolean startedHyperlink = startHyperlink(image,false);

			String imageName = getImagePath(renderer, image.isLazy(), gridCell);
			drawingRelsHelper.exportImage(imageName);

			sheetHelper.exportMergedCells(rowIndex, colIndex, gridCell.getRowSpan(), gridCell.getColSpan());
			
			drawingHelper.write("<xdr:twoCellAnchor editAs=\"oneCell\">\n");
			drawingHelper.write("<xdr:from><xdr:col>" +
				colIndex +
				"</xdr:col><xdr:colOff>" +
				LengthUtil.emu(leftPadding) +
				"</xdr:colOff><xdr:row>" +
				rowIndex +
				"</xdr:row><xdr:rowOff>" +
				LengthUtil.emu(topPadding) +
				"</xdr:rowOff></xdr:from>\n");
			drawingHelper.write("<xdr:to><xdr:col>" +
				(colIndex + gridCell.getColSpan()) +
				"</xdr:col><xdr:colOff>" +
				LengthUtil.emu(-rightPadding) +
				"</xdr:colOff><xdr:row>" +
				(rowIndex + (isCollapseRowSpan ? 1 : gridCell.getRowSpan())) +
				"</xdr:row><xdr:rowOff>" +
				LengthUtil.emu(-bottomPadding) +
				"</xdr:rowOff></xdr:to>\n");
			
			drawingHelper.write("<xdr:pic>\n");
			drawingHelper.write("<xdr:nvPicPr><xdr:cNvPr id=\"" + (image.hashCode() > 0 ? image.hashCode() : -image.hashCode()) + "\" name=\"Picture\">\n");

			String href = getHyperlinkURL(image);
			if (href != null)
			{
				drawingHelper.exportHyperlink(href);
			}
			
			drawingHelper.write("</xdr:cNvPr><xdr:cNvPicPr/></xdr:nvPicPr>\n");
			drawingHelper.write("<xdr:blipFill>\n");
			drawingHelper.write("<a:blip r:embed=\"" + imageName + "\"/>");
			drawingHelper.write("<a:srcRect");
////			if (cropLeft > 0)
//				drawingHelper.write(" l=\"" + (int)cropLeft + "\"");
////			if (cropTop > 0)
//				drawingHelper.write(" t=\"" + (int)cropTop + "\"");
////			if (cropRight > 0)
//				drawingHelper.write(" r=\"" + (int)cropRight + "\"");
////			if (cropBottom > 0)
//				drawingHelper.write(" b=\"" + (int)cropBottom + "\"");
			drawingHelper.write("/>");
			drawingHelper.write("<a:stretch><a:fillRect");
//			if (cropLeft > 0)
				drawingHelper.write(" l=\"" + (int)cropLeft + "\"");
//			if (cropTop > 0)
				drawingHelper.write(" t=\"" + (int)cropTop + "\"");
//			if (cropRight > 0)
				drawingHelper.write(" r=\"" + (int)cropRight + "\"");
//			if (cropBottom > 0)
				drawingHelper.write(" b=\"" + (int)cropBottom + "\"");
			drawingHelper.write("/></a:stretch>\n");
			drawingHelper.write("</xdr:blipFill>\n");
			drawingHelper.write("<xdr:spPr><a:xfrm><a:off x=\"0\" y=\"0\"/><a:ext cx=\"" + LengthUtil.emu(0) + "\" cy=\"" + LengthUtil.emu(0) + "\"/>");
			drawingHelper.write("</a:xfrm><a:prstGeom prst=\"rect\"></a:prstGeom>\n");
//			if (image.getModeValue() == ModeEnum.OPAQUE && image.getBackcolor() != null)
//			{
//				drawingHelper.write("<a:solidFill><a:srgbClr val=\"" + JRColorUtil.getColorHexa(image.getBackcolor()) + "\"/></a:solidFill>\n");
//			}
			drawingHelper.write("</xdr:spPr>\n");
			drawingHelper.write("</xdr:pic>\n");
			drawingHelper.write("<xdr:clientData/>\n");
			drawingHelper.write("</xdr:twoCellAnchor>\n");

//			if(startedHyperlink)
//			{
//				endHyperlink(false);
//			}
		}

//		drawingHelper.write("</w:p>");

		cellHelper.exportFooter();
	}


	protected void exportLine(
		JRPrintLine line, 
		JRExporterGridCell gridCell,
		int colIndex, 
		int rowIndex
		) throws JRException 
	{
		JRLineBox box = new JRBaseLineBox(null);
		JRPen pen = null;
		float ratio = line.getWidth() / line.getHeight();
		if (ratio > 1)
		{
			if (line.getDirectionValue() == LineDirectionEnum.TOP_DOWN)
			{
				pen = box.getTopPen();
			}
			else
			{
				pen = box.getBottomPen();
			}
		}
		else
		{
			if (line.getDirectionValue() == LineDirectionEnum.TOP_DOWN)
			{
				pen = box.getLeftPen();
			}
			else
			{
				pen = box.getRightPen();
			}
		}
		pen.setLineColor(line.getLinePen().getLineColor());
		pen.setLineStyle(line.getLinePen().getLineStyleValue());
		pen.setLineWidth(line.getLinePen().getLineWidth());

		gridCell.setBox(box);//CAUTION: only some exporters set the cell box
		
		cellHelper.exportHeader(gridCell, rowIndex, colIndex);
		sheetHelper.exportMergedCells(rowIndex, colIndex, gridCell.getRowSpan(), gridCell.getColSpan());
		cellHelper.exportFooter();
	}


	protected void exportRectangle(
		JRPrintGraphicElement rectangle,
		JRExporterGridCell gridCell, 
		int colIndex, 
		int rowIndex
		) throws JRException 
	{
		JRLineBox box = new JRBaseLineBox(null);
		JRPen pen = box.getPen();
		pen.setLineColor(rectangle.getLinePen().getLineColor());
		pen.setLineStyle(rectangle.getLinePen().getLineStyleValue());
		pen.setLineWidth(rectangle.getLinePen().getLineWidth());

		gridCell.setBox(box);//CAUTION: only some exporters set the cell box
		
		cellHelper.exportHeader(gridCell, rowIndex, colIndex);
		sheetHelper.exportMergedCells(rowIndex, colIndex, gridCell.getRowSpan(), gridCell.getColSpan());
		cellHelper.exportFooter();
	}


	public void exportText(
		final JRPrintText text, 
		JRExporterGridCell gridCell,
		int colIndex, 
		int rowIndex
		) throws JRException
	{
		final JRStyledText styledText = getStyledText(text);

		final int textLength = styledText == null ? 0 : styledText.length();

		final String textStr = styledText.getText();

		TextValue textValue = null;
		String pattern = null;
		if (isDetectCellType)
		{
			textValue = getTextValue(text, textStr);
			if (textValue instanceof NumberTextValue)
			{
				pattern = ((NumberTextValue)textValue).getPattern();
			}
			else if (textValue instanceof DateTextValue)
			{
				pattern = ((DateTextValue)textValue).getPattern();
			}
		}
		
		cellHelper.exportHeader(
			gridCell, rowIndex, colIndex, textValue, 
			getConvertedPattern(text, pattern), 
			getTextLocale(text), 
			isWrapText(gridCell.getElement()) || ((JRXlsxExporterNature)nature).getColumnAutoFit(gridCell.getElement()), 
			isCellHidden(gridCell.getElement()), 
			isCellLocked(gridCell.getElement())
			);
		sheetHelper.exportMergedCells(rowIndex, colIndex, gridCell.getRowSpan(), gridCell.getColSpan());

		String textFormula = getFormula(text);
		if (textFormula != null)
		{
			sheetHelper.write("<f>" + textFormula + "</f>\n");
		}

//		if (text.getLineSpacing() != JRTextElement.LINE_SPACING_SINGLE)
//		{
//			styleBuffer.append("line-height: " + text.getLineSpacingFactor() + "; ");
//		}

//		if (styleBuffer.length() > 0)
//		{
//			writer.write(" style=\"");
//			writer.write(styleBuffer.toString());
//			writer.write("\"");
//		}
//
//		writer.write(">");
		
//		tableHelper.getParagraphHelper().exportProps(text);
		
//		insertPageAnchor();
//		if (text.getAnchorName() != null)
//		{
//			tempBodyWriter.write("<text:bookmark text:name=\"");
//			tempBodyWriter.write(text.getAnchorName());
//			tempBodyWriter.write("\"/>");
//		}

		String href = getHyperlinkURL(text);
		if (href != null)
		{
			sheetHelper.exportHyperlink(rowIndex, colIndex, href);
		}

		
		TextValueHandler handler = 
			new TextValueHandler() 
			{
				public void handle(BooleanTextValue textValue) throws JRException {
					sheetHelper.write("<v>" + textValue.getValue() + "</v>");
				}
				
				public void handle(DateTextValue textValue) throws JRException {
					Date date = textValue.getValue();
					sheetHelper.write(
						"<v>" 
						+ (date == null ? "" : JRDataUtils.getExcelSerialDayNumber(
							date, 
							getTextLocale(text), 
							getTextTimeZone(text)
							)) 
						+ "</v>"
						);
				}
				
				public void handle(NumberTextValue textValue) throws JRException {
					Number number = textValue.getValue();
					sheetHelper.write(
						"<v>" 
						+ (number == null ? "" : number) 
						+ "</v>"
						);
				}
				
				public void handle(StringTextValue textValue) throws JRException {
					writeText();
				}
				
				private void writeText() throws JRException {
					sheetHelper.write("<is>");//FIXMENOW make writer util; check everywhere
	
					if (textLength > 0)
					{
						exportStyledText(text.getStyle(), styledText, getTextLocale(text));
					}
	
					sheetHelper.write("</is>");
				}
			};
		
		if (textValue != null)
		{
			//detect cell type
			textValue.handle(handler);
		}
		else
		{
			handler.handle((StringTextValue)null);
		}
		
		sheetHelper.flush();

		cellHelper.exportFooter();
	}


	protected void exportGenericElement(
		JRGenericPrintElement element, 
		JRExporterGridCell gridCell, 
		int colIndex, 
		int rowIndex, 
		int emptyCols,
		int yCutsRow, 
		JRGridLayout layout
		) throws JRException
	{
		GenericElementXlsxHandler handler = (GenericElementXlsxHandler) 
			GenericElementHandlerEnviroment.getInstance(getJasperReportsContext()).getElementHandler(
				element.getGenericType(), XLSX_EXPORTER_KEY);

		if (handler != null)
		{
			handler.exportElement(exporterContext, element, gridCell, colIndex, rowIndex);
		}
		else
		{
			if (log.isDebugEnabled())
			{
				log.debug("No XLSX generic element handler for " 
						+ element.getGenericType());
			}
		}
	}


	protected ExporterNature getNature() 
	{
		return nature;
	}


	protected void openWorkbook(OutputStream os) throws JRException 
	{
		rendererToImagePathMap = new HashMap<String,String>();
//		imageMaps = new HashMap();
		imagesToProcess = new ArrayList<JRPrintElementIndex>();
//		hyperlinksMap = new HashMap();

		try
		{
			xlsxZip = new XlsxZip(jasperReportsContext);

			wbHelper = new XlsxWorkbookHelper(xlsxZip.getWorkbookEntry().getWriter());
			wbHelper.exportHeader();

			relsHelper = new XlsxRelsHelper(xlsxZip.getRelsEntry().getWriter());
			ctHelper = new XlsxContentTypesHelper(xlsxZip.getContentTypesEntry().getWriter());
			if(macroTemplate != null)
			{
				xlsxZip.addMacro(macroTemplate);
				relsHelper.setContainsMacro(true);
				ctHelper.setContainsMacro(true);
			}
			relsHelper.exportHeader();
			ctHelper.exportHeader();
			
			styleHelper = 
				new XlsxStyleHelper(
					xlsxZip.getStylesEntry().getWriter(), 
					fontMap, 
					getExporterKey(),
					isWhitePageBackground,
					isIgnoreCellBorder,
					isIgnoreCellBackground,
					isFontSizeFixEnabled
					);
			
			firstPageNotSet = true;
		}
		catch (IOException e)
		{
			throw new JRException(e);
		}

//		runHelper = new RunHelper(sheetWriter, fontMap, null);//FIXMEXLSX check this null
	}


	protected void removeColumn(int col) {
		//column width was already set to zero
	}


	protected void setBackground() {
		// TODO Auto-generated method stub
		
	}


	protected void setCell(JRExporterGridCell gridCell, int colIndex, int rowIndex) 
	{
	}


	protected void addOccupiedCell(OccupiedGridCell occupiedGridCell, int colIndex, int rowIndex) 
	{
		ElementGridCell elementGridCell = (ElementGridCell)occupiedGridCell.getOccupier();
		cellHelper.exportHeader(elementGridCell, rowIndex, colIndex);
		cellHelper.exportFooter();
	}


	protected void setColumnWidth(int col, int width, boolean autoFit) 
	{
		sheetHelper.exportColumn(col, width, autoFit);
	}


	protected void updateColumn(int col, boolean autoFit) 
	{
	}


	protected void setRowHeight(
			int rowIndex, 
			int rowHeight,
			Cut yCut,
			XlsRowLevelInfo levelInfo
			) throws JRException 
		{
			sheetHelper.exportRow(rowHeight, yCut, levelInfo);
		}

	/**
	 *
	 */
	protected String getExporterKey()
	{
		return XLSX_EXPORTER_KEY;
	}
	
	protected void setFreezePane(int rowIndex, int colIndex, boolean isRowEdge, boolean isColumnEdge)
	{
		//TODO: set freeze pane for element-level defined indexes
	}

	protected void setSheetName(String sheetName)
	{
		/* nothing to do here; it's done in createSheet() */
	}

	@Override
	protected void setAutoFilter(String autoFilterRange)
	{
		sheetAutoFilter = autoFilterRange;
	}
	
	protected void resetAutoFilters()
	{
		super.resetAutoFilters();
		sheetAutoFilter = null;
	}


	@Override
	protected void setRowLevels(XlsRowLevelInfo levelInfo, String level) 
	{
		/* nothing to do here; it's done in setRowHeight */
	}
	
	public String getMacroTemplatePath() {
		return macroTemplate;
	}


	public void setMacroTemplate(String macroTemplate) {
		this.macroTemplate = macroTemplate;
	}
	
	protected void setScale(Integer scale)
	{
		/* nothing to do here; it's already done in the abstract exporter */
	}
	
}

