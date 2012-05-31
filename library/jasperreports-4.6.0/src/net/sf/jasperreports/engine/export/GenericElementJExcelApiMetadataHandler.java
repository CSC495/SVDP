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
package net.sf.jasperreports.engine.export;

import java.util.List;
import java.util.Map;

import net.sf.jasperreports.engine.JRGenericPrintElement;

/**
 * A generic print element XLS export handler.
 * 
 * @author sanda zaharia (shertage@users.sourceforge.net)
 * @version $Id: GenericElementJExcelApiMetadataHandler.java 5180 2012-03-29 13:23:12Z teodord $
 */
public interface GenericElementJExcelApiMetadataHandler extends GenericElementHandler
{

	/**
	 * Exports a generic element.
	 * 
	 * <p>
	 * Access to the exporter output and environment is provided via the
	 * {@link JExcelApiExporterContext} argument.
	 * 
	 * @param exporterContext the exporter context
	 * @param element the generic element to export
	 * @param currentRow the map with column names as keys and exported CellValue objects as values 
	 * @param repeatedValues the map containing CellValue objects to be placed instead in the currentRow map when the exported object is null
	 * @param columnNames the list of column names
	 * @param columnNamesMap the map containing the mapping between column names and column indexes
	 * @param currentColumnName the current column's name
	 * @param colIndex the current column index
	 * @param rowIndex the current row index
	 * @param repeatValue the flag specifying if null values should be replaced with objects from the repeatedValues map
	 * 
	 * @return int value representing the resulting row index, if this was incremented during export
	 */
	int exportElement(
			JExcelApiExporterContext exporterContext, 
			JRGenericPrintElement element, 
			Map<String, Object> currentRow, 
			Map<String, Object> repeatedValues,
			List<String> columnNames,
			Map<String, Integer> columnNamesMap,
			String currentColumnName, 
			int colIndex, 
			int rowIndex,
			boolean repeatValue
			);

}
