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
package net.sf.jasperreports.engine.xml;

import net.sf.jasperreports.engine.design.JRDesignPropertyExpression;

import org.xml.sax.Attributes;

/**
 * {@link JRDesignPropertyExpression} factory.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRPropertyExpressionFactory.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class JRPropertyExpressionFactory extends JRBaseFactory
{

	public Object createObject(Attributes attrs) throws Exception
	{
		JRDesignPropertyExpression propertyExpression = new JRDesignPropertyExpression();
		
		String name = attrs.getValue(JRXmlConstants.ATTRIBUTE_name);
		propertyExpression.setName(name);
		
		return propertyExpression;
	}

}
