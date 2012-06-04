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
package net.sf.jasperreports.components.html;

import net.sf.jasperreports.engine.JRExpressionCollector;
import net.sf.jasperreports.engine.base.JRBaseObjectFactory;
import net.sf.jasperreports.engine.component.Component;
import net.sf.jasperreports.engine.component.ComponentCompiler;
import net.sf.jasperreports.engine.design.JRVerifier;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: HtmlComponentCompiler.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class HtmlComponentCompiler implements ComponentCompiler {

	public void collectExpressions(Component component, JRExpressionCollector collector) {
		HtmlComponent htmlComponent = (HtmlComponent) component;
		collector.addExpression(htmlComponent.getHtmlContentExpression());
	}
	
	public Component toCompiledComponent(Component component,
			JRBaseObjectFactory baseFactory) {
		HtmlComponent htmlComponent = (HtmlComponent) component;
		return new HtmlComponent(htmlComponent, baseFactory);
	}

	public void verify(Component component, JRVerifier verifier) {
		// TODO
	}
	
}
