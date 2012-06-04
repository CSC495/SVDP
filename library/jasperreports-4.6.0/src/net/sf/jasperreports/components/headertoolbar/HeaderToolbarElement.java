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
package net.sf.jasperreports.components.headertoolbar;

import net.sf.jasperreports.engine.JRGenericElementType;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.xml.JRXmlConstants;


/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: HeaderToolbarElement.java 5111 2012-03-21 15:46:01Z narcism $
 */
public interface HeaderToolbarElement {
	
	public static final String ELEMENT_NAME = "headertoolbar";
	public static final JRGenericElementType ELEMENT_TYPE = new JRGenericElementType(JRXmlConstants.JASPERREPORTS_NAMESPACE, ELEMENT_NAME);
	
	public static final String PARAM_COLUMN_LABEL_PREFIX = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.column.";

	public static final String SORT_ORDER_ASC = "Asc";
	public static final String SORT_ORDER_DESC = "Dsc";
	public static final String SORT_ORDER_NONE = "None";
	public static final String SORT_COLUMN_TOKEN_SEPARATOR = ":";
	
	public static final String PARAMETER_TABLE_UUID = "tableUUID";
	public static final String PARAMETER_COLUMN_NAME = "sortColumnName";
	public static final String PARAMETER_COLUMN_LABEL = "sortColumnLabel";
	public static final String PARAMETER_COLUMN_TYPE = "sortColumnType";
	
	public static final String PROPERTY_FILTER_TYPE = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.filter.type";
	public static final String PROPERTY_FILTER_PATTERN = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.filter.pattern";
	
	public static final String PROPERTY_CAN_SORT = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.can.sort";
	public static final String PROPERTY_CAN_FILTER = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.can.filter";
	public static final String PROPERTY_CAN_MOVE = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.can.move";
	public static final String PROPERTY_CAN_RESIZE = JRPropertiesUtil.PROPERTY_PREFIX + "export.headertoolbar.can.resize";

}