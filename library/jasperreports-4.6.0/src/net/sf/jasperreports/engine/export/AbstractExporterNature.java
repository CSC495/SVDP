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
 * Greg Hilton 
 */

package net.sf.jasperreports.engine.export;

import java.util.Map;

import net.sf.jasperreports.engine.JRPrintElement;
import net.sf.jasperreports.engine.JRPropertiesUtil;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: AbstractExporterNature.java 5377 2012-05-11 13:50:50Z shertage $
 */
public abstract class AbstractExporterNature implements ExporterNature
{
	protected final JasperReportsContext jasperReportsContext;
	protected final JRPropertiesUtil propertiesUtil;
	protected ExporterFilter filter;

	/**
	 * 
	 */
	protected AbstractExporterNature(
		JasperReportsContext jasperReportsContext,
		ExporterFilter filter 
		)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.propertiesUtil = JRPropertiesUtil.getInstance(jasperReportsContext);
		this.filter = filter;
	}
	
	/**
	 *
	 */
	public JRPropertiesUtil getPropertiesUtil()
	{
		return propertiesUtil;
	}

	public void setXProperties(CutsInfo xCuts, JRPrintElement element, int row1, int col1, int row2, int col2)
	{
		// nothing to do here
	}
	
	public void setXProperties(Map<String,Object> xCutsProperties, JRPrintElement element)
	{
		// nothing to do here
	}
	
	public void setYProperties(CutsInfo yCuts, JRPrintElement element, int row1, int col1, int row2, int col2)
	{
		// nothing to do here
	}
	
	public void setYProperties(Map<String,Object> yCutsProperties, JRPrintElement element)
	{
		// nothing to do here
	}
	
}
