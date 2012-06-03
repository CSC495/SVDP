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
package net.sf.jasperreports.components.table.fill;

import net.sf.jasperreports.engine.ElementDecorator;
import net.sf.jasperreports.engine.JRDatasetParameter;
import net.sf.jasperreports.engine.JRDatasetRun;
import net.sf.jasperreports.engine.JRExpression;
import net.sf.jasperreports.engine.JRSubreport;
import net.sf.jasperreports.engine.JRSubreportParameter;
import net.sf.jasperreports.engine.JRSubreportReturnValue;
import net.sf.jasperreports.engine.component.FillContext;

/**
 * 
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: TableSubreport.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class TableSubreport extends ElementDecorator implements JRSubreport
{
	
	private final JRDatasetRun datasetRun;
	private final JRSubreportParameter[] parameters;

	public TableSubreport(JRDatasetRun datasetRun, FillContext fillContext)
	{
		super(fillContext.getComponentElement());
		
		this.datasetRun = datasetRun;
		
		JRDatasetParameter[] datasetParameters = datasetRun.getParameters();
		if (datasetParameters == null)
		{
			this.parameters = null;
		}
		else
		{
			this.parameters = new JRSubreportParameter[datasetParameters.length];
			for (int i = 0; i < datasetParameters.length; i++)
			{
				JRDatasetParameter datasetParameter = datasetParameters[i];
				TableSubreportParameter subreportParameter = 
					new TableSubreportParameter(datasetParameter);
				this.parameters[i] = subreportParameter;
			}
		}
	}

	public JRExpression getConnectionExpression()
	{
		return datasetRun.getConnectionExpression();
	}

	public JRExpression getDataSourceExpression()
	{
		return datasetRun.getDataSourceExpression();
	}

	public JRExpression getExpression()
	{
		// no report expression
		return null;
	}

	public JRSubreportParameter[] getParameters()
	{
		return parameters;
	}

	public JRExpression getParametersMapExpression()
	{
		return datasetRun.getParametersMapExpression();
	}

	public JRSubreportReturnValue[] getReturnValues()
	{
		// no return values
		return null;
	}

	/**
	 * @deprecated Replaced by {@link #getUsingCache()}.
	 */
	public Boolean isOwnUsingCache()
	{
		return false;
	}

	public Boolean getUsingCache()
	{
		return Boolean.FALSE;
	}

	public Boolean isRunToBottom()
	{
		return false;
	}

	/**
	 * @deprecated Replaced by {@link #getUsingCache()}.
	 */
	public boolean isUsingCache()
	{
		return false;
	}

	public void setRunToBottom(Boolean runToBottom)
	{
		throw new UnsupportedOperationException();
	}

	public void setUsingCache(Boolean isUsingCache)
	{
		throw new UnsupportedOperationException();
	}

}
