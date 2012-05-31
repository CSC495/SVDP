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
 * Special thanks to Google 'Summer of Code 2005' program for supporting this development
 * 
 * Contributors:
 * Majid Ali Khan - majidkk@users.sourceforge.net
 * Frank Sch�nheit - Frank.Schoenheit@Sun.COM
 */
package net.sf.jasperreports.engine.export.oasis;

import java.io.IOException;
import java.io.Writer;
import java.util.Collection;
import java.util.Iterator;

import net.sf.jasperreports.engine.export.zip.ExportZipEntry;



/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ContentBuilder.java 5180 2012-03-29 13:23:12Z teodord $
 */
public class ContentBuilder
{
	/**
	 *
	 */
	public static final String VERSION = "1.1";

	/**
	 * 
	 */
	private ExportZipEntry contentEntry;
	private ExportZipEntry styleEntry;
	private ExportZipEntry bodyEntry;
	
	private Collection<String> fontFaces;
	
	private byte openDocumentNature;
	
	/**
	 * 
	 */
	public ContentBuilder(
		ExportZipEntry contentEntry,
		ExportZipEntry styleEntry,
		ExportZipEntry bodyEntry,
		Collection<String> fontFaces
		)
	{
		this(
			contentEntry,
			styleEntry,
			bodyEntry,
			fontFaces,
			JROpenDocumentExporterNature.ODT_NATURE
			);
	}
	
	/**
	 * 
	 */
	public ContentBuilder(
		ExportZipEntry contentEntry,
		ExportZipEntry styleEntry,
		ExportZipEntry bodyEntry,
		Collection<String> fontFaces,
		byte openDocumentNature
		)
	{
		this.contentEntry = contentEntry;
		this.styleEntry = styleEntry;
		this.bodyEntry = bodyEntry;
		this.fontFaces = fontFaces;
		this.openDocumentNature = openDocumentNature;
	}
	

	public void build() throws IOException
	{
		String mimetype;
		switch(openDocumentNature)
		{
			case JROpenDocumentExporterNature.ODS_NATURE:
				mimetype = "spreadsheet";
				break;
			case JROpenDocumentExporterNature.ODT_NATURE:
			default:
				mimetype = "text";
		}
		Writer writer = contentEntry.getWriter();
		
		writer.write("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

		writer.write("<office:document-content");
		writer.write(" xmlns:office=\"urn:oasis:names:tc:opendocument:xmlns:office:1.0\"");
		writer.write(" xmlns:style=\"urn:oasis:names:tc:opendocument:xmlns:style:1.0\"");
		writer.write(" xmlns:text=\"urn:oasis:names:tc:opendocument:xmlns:text:1.0\"");
		writer.write(" xmlns:table=\"urn:oasis:names:tc:opendocument:xmlns:table:1.0\"");
		writer.write(" xmlns:draw=\"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0\"");
		writer.write(" xmlns:fo=\"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0\"");
		writer.write(" xmlns:xlink=\"http://www.w3.org/1999/xlink\"");
		writer.write(" xmlns:dc=\"http://purl.org/dc/elements/1.1/\"");
		writer.write(" xmlns:meta=\"urn:oasis:names:tc:opendocument:xmlns:meta:1.0\"");
		writer.write(" xmlns:number=\"urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0\"");
		writer.write(" xmlns:presentation=\"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0\"");
		writer.write(" xmlns:svg=\"urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0\"");
		writer.write(" xmlns:chart=\"urn:oasis:names:tc:opendocument:xmlns:chart:1.0\"");
		writer.write(" xmlns:dr3d=\"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0\"");
		writer.write(" xmlns:math=\"http://www.w3.org/1998/Math/MathML\"");
		writer.write(" xmlns:form=\"urn:oasis:names:tc:opendocument:xmlns:form:1.0\"");
		writer.write(" xmlns:script=\"urn:oasis:names:tc:opendocument:xmlns:script:1.0\"");
		writer.write(" xmlns:ooo=\"http://openoffice.org/2004/office\"");
		writer.write(" xmlns:ooow=\"http://openoffice.org/2004/writer\"");
		writer.write(" xmlns:oooc=\"http://openoffice.org/2004/calc\"");
		writer.write(" xmlns:dom=\"http://www.w3.org/2001/xml-events\"");
		writer.write(" xmlns:xforms=\"http://www.w3.org/2002/xforms\"");
		writer.write(" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"");
		writer.write(" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"");
		writer.write(" office:version=\"");
		writer.write(VERSION);
		writer.write("\">\n");

		writer.write(" <office:scripts/>\n");
		
		writer.write(" <office:font-face-decls>\n");
		if (fontFaces != null)
		{
			for (Iterator<String> it = fontFaces.iterator(); it.hasNext();)
			{
				String fontFace = it.next();
				writer.write("<style:font-face style:name=\"" + fontFace + "\"");
				writer.write(" svg:font-family=\"" + fontFace + "\"");
				writer.write("/>\n");
			}
		}
		writer.write(" </office:font-face-decls>\n");
		
		writer.write(" <office:automatic-styles>\n");
		
		writer.flush();
		styleEntry.writeData(contentEntry.getOutputStream());

		writer.write(" <style:style style:name=\"empty-cell\" style:family=\"table-cell\">\n");
		writer.write("  <style:table-cell-properties fo:wrap-option=\"wrap\" style:shrink-to-fit=\"false\"");
		writer.write(" fo:border=\"0in solid #000000\"/>\n");
		writer.write(" </style:style>\n");
		writer.write(" </office:automatic-styles>\n");
		
		writer.write("<office:body><office:" + mimetype + ">\n");
		writer.write("<office:forms form:automatic-focus=\"false\" form:apply-design-mode=\"false\"/>\n");
		writer.flush();
		bodyEntry.writeData(contentEntry.getOutputStream());
		writer.write("</office:" + mimetype + ">\n</office:body>\n");

		writer.write("</office:document-content>\n");
		
		writer.flush();
		writer.close();
		
	}

}
