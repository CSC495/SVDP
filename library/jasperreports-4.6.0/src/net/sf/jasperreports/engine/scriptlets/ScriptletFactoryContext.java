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
package net.sf.jasperreports.engine.scriptlets;

import java.util.Map;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataset;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReport;
import net.sf.jasperreports.engine.JasperReportsContext;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: ScriptletFactoryContext.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class ScriptletFactoryContext
{

	/**
	 *
	 */
	private JasperReportsContext jasperReportsContext;
	private JasperReport jasperReport;
	private JRDataset dataset;
	private Map<String,Object> parameterValues;

	/**
	 *
	 */
	public ScriptletFactoryContext(
		JasperReportsContext jasperReportsContext,
		JRDataset dataset,
		Map<String,Object> parameterValues 
		)
	{
		this.jasperReportsContext = jasperReportsContext;
		this.jasperReport = (JasperReport)parameterValues.get(JRParameter.JASPER_REPORT);
		this.dataset = dataset;
		this.parameterValues = parameterValues;
	}

	/**
	 * @deprecated Replaced by {@link #ScriptletFactoryContext(JasperReportsContext, JRDataset, Map)}.
	 */
	public ScriptletFactoryContext(Map<String,Object> parameterValues, JRDataset dataset)
	{
		this(DefaultJasperReportsContext.getInstance(), dataset, parameterValues);
	}

	/**
	 * 
	 */
	public JasperReportsContext getJasperReportsContext()
	{
		return jasperReportsContext;
	}

	/**
	 * Returns the {@link JasperReport} object for this context.
	 * 
	 * <p>
	 * Note that this context might correspond to a subdataset in the report.
	 * Use {@link #getDataset()} to retrieve the dataset for which scriptlets
	 * are to be created
	 * </p>
	 * 
	 * @return the current {@link JasperReport} object
	 * @see #getDataset()
	 */
	public JasperReport getJasperReport()
	{
		return jasperReport;
	}
	
	/**
	 *
	 */
	public Map<String,Object> getParameterValues()
	{
		return parameterValues;
	}
	
	/**
	 * Returns the dataset for which scriptlets are to be created.
	 * 
	 * @return a dataset
	 */
	public JRDataset getDataset()
	{
		return dataset;
	}
}
