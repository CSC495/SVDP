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
package net.sf.jasperreports.data.empty;

import java.util.Map;

import net.sf.jasperreports.data.AbstractDataAdapterService;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JREmptyDataSource;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: EmptyDataAdapterService.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class EmptyDataAdapterService extends AbstractDataAdapterService 
{
	
	/**
	 * 
	 */
	public EmptyDataAdapterService(JasperReportsContext jasperReportsContext, EmptyDataAdapter emptyDataAdapter)
	{
		super(jasperReportsContext, emptyDataAdapter);
	}
	
	/**
	 * @deprecated Replaced by {@link #EmptyDataAdapterService(JasperReportsContext, EmptyDataAdapter)}.
	 */
	public EmptyDataAdapterService(EmptyDataAdapter emptyDataAdapter)
	{
		this(DefaultJasperReportsContext.getInstance(), emptyDataAdapter);
	}
	
	public EmptyDataAdapter getEmptyDataAdapter()
	{
		return (EmptyDataAdapter)getDataAdapter();
	}
	
	@Override
	public void contributeParameters(Map<String, Object> parameters) throws JRException
	{
		EmptyDataAdapter emptyDataAdapter = getEmptyDataAdapter();
		if (emptyDataAdapter != null)
		{
			parameters.put(JRParameter.REPORT_DATA_SOURCE, new JREmptyDataSource(emptyDataAdapter.getRecordCount()));
		}
	}
	
}
