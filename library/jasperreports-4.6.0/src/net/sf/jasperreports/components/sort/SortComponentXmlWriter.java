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
package net.sf.jasperreports.components.sort;

import java.io.IOException;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.component.Component;
import net.sf.jasperreports.engine.component.ComponentKey;
import net.sf.jasperreports.engine.component.ComponentXmlWriter;
import net.sf.jasperreports.engine.component.ComponentsEnvironment;
import net.sf.jasperreports.engine.type.EvaluationTimeEnum;
import net.sf.jasperreports.engine.util.JRXmlWriteHelper;
import net.sf.jasperreports.engine.util.XmlNamespace;
import net.sf.jasperreports.engine.xml.JRXmlWriter;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: SortComponentXmlWriter.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class SortComponentXmlWriter implements ComponentXmlWriter 
{
	private JasperReportsContext jasperReportsContext;
	
	/**
	 * @deprecated Replaced by {@link #SortComponentXmlWriter(JasperReportsContext)}.
	 */
	public SortComponentXmlWriter()
	{
		this(DefaultJasperReportsContext.getInstance());
	}

	/**
	 * 
	 */
	public SortComponentXmlWriter(JasperReportsContext jasperReportsContext)
	{
		this.jasperReportsContext = jasperReportsContext;
	}

	public void writeToXml(ComponentKey componentKey, Component component,
			JRXmlWriter reportWriter) throws IOException {
		if (component instanceof SortComponent) {
			SortComponent sortComponent = (SortComponent) component;
			writeSortComponent(sortComponent, componentKey, reportWriter);
		}
	}
	
	protected void writeSortComponent(SortComponent sortComponent, ComponentKey componentKey,
			JRXmlWriter reportWriter) throws IOException {
		JRXmlWriteHelper writer = reportWriter.getXmlWriteHelper();
		
		String namespaceURI = componentKey.getNamespace();
		String schemaLocation = 
			ComponentsEnvironment.getInstance(jasperReportsContext).getBundle(namespaceURI).getXmlParser().getPublicSchemaLocation();
		XmlNamespace componentNamespace = new XmlNamespace(namespaceURI, componentKey.getNamespacePrefix(),
				schemaLocation);
		
		writer.startElement("sort", componentNamespace);
		
		
		if (sortComponent.getEvaluationTime() != EvaluationTimeEnum.NOW) {
			writer.addAttribute(SortComponent.PROPERTY_EVALUATION_TIME, sortComponent.getEvaluationTime());
		}
		writer.addAttribute(SortComponent.PROPERTY_EVALUATION_GROUP, sortComponent.getEvaluationGroup());
		
		// write symbol settings
		writer.startElement("symbol");
		if (sortComponent.getHandlerColor() != null)
		{
			writer.addAttribute(SortComponent.PROPERTY_HANDLER_COLOR, sortComponent.getHandlerColor());
		}
		writer.addAttribute(SortComponent.PROPERTY_COLUMN_TYPE, sortComponent.getSortFieldType());
		writer.addAttribute(SortComponent.PROPERTY_COLUMN_NAME, sortComponent.getSortFieldName());
		writer.addAttribute(SortComponent.PROPERTY_HANDLER_HORIZONTAL_ALIGN, sortComponent.getHandlerHorizontalAlign());
		writer.addAttribute(SortComponent.PROPERTY_HANDLER_VERTICAL_ALIGN, sortComponent.getHandlerVerticalAlign());
		reportWriter.writeFont(sortComponent.getSymbolFont());
		writer.closeElement();

		writer.closeElement();
	}
}
