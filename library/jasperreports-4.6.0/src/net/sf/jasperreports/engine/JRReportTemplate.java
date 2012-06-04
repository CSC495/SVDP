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
package net.sf.jasperreports.engine;

import net.sf.jasperreports.engine.xml.JRXmlTemplateLoader;


/**
 * A template included in a report.
 * <p/>
 * A template inclusion in a report consits of an expression that should be
 * resolved at runtime to a {@link JRTemplate JRTemplate} instance.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRReportTemplate.java 4595 2011-09-08 15:55:10Z teodord $
 * @see JRReport#getTemplates()
 */
public interface JRReportTemplate
{

	/**
	 * Returns the template source expression.
	 * <p/>
	 * The expression type should be (compatible with) one of <code>java.lang.String</code>,
	 * <code>java.io.File</code>, <code>java.net.URL</code>, <code>java.io.InputStream</code>
	 * (in which cases the template is loaded via {@link JRXmlTemplateLoader}) or
	 * <code>net.sf.jasperreports.engine.JRTemplate</code> .
	 * 
	 * @return the template source expression
	 */
	JRExpression getSourceExpression();

}
