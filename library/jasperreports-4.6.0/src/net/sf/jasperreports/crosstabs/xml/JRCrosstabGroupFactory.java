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
package net.sf.jasperreports.crosstabs.xml;

import net.sf.jasperreports.crosstabs.design.JRDesignCrosstabGroup;
import net.sf.jasperreports.crosstabs.type.CrosstabTotalPositionEnum;
import net.sf.jasperreports.engine.xml.JRBaseFactory;

import org.xml.sax.Attributes;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: JRCrosstabGroupFactory.java 5180 2012-03-29 13:23:12Z teodord $
 */
public abstract class JRCrosstabGroupFactory extends JRBaseFactory
{
	public static final String ELEMENT_columnGroup = "columnGroup";
	public static final String ELEMENT_crosstabColumnHeader = "crosstabColumnHeader";
	public static final String ELEMENT_crosstabTotalColumnHeader = "crosstabTotalColumnHeader";

	public static final String ATTRIBUTE_name = "name";
	public static final String ATTRIBUTE_totalPosition = "totalPosition";
	
	protected final void setGroupAtts(Attributes atts, JRDesignCrosstabGroup group)
	{
		group.setName(atts.getValue(ATTRIBUTE_name));
		
		CrosstabTotalPositionEnum totalPosition = CrosstabTotalPositionEnum.getByName(atts.getValue(ATTRIBUTE_totalPosition));
		if (totalPosition != null)
		{
			group.setTotalPosition(totalPosition);
		}
	}
}
