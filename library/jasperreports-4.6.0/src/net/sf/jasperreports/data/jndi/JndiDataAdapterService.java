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
package net.sf.jasperreports.data.jndi;

import java.sql.Connection;
import java.util.Map;

import javax.naming.Context;
import javax.naming.InitialContext;
import javax.sql.DataSource;

import net.sf.jasperreports.data.AbstractDataAdapterService;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReportsContext;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;


/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JndiDataAdapterService.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class JndiDataAdapterService extends AbstractDataAdapterService 
{
	private static final Log log = LogFactory.getLog(JndiDataAdapterService.class);
	
	private Connection connection = null; 

	/**
	 * 
	 */
	public JndiDataAdapterService(JasperReportsContext jasperReportsContext, JndiDataAdapter jndiDataAdapter)
	{
		super(jasperReportsContext, jndiDataAdapter);
	}
	
	/**
	 * @deprecated Replaced by {@link #JndiDataAdapterService(JasperReportsContext, JndiDataAdapter)}. 
	 */
	public JndiDataAdapterService(JndiDataAdapter jndiDataAdapter)
	{
		this(DefaultJasperReportsContext.getInstance(), jndiDataAdapter);
	}
	
	public JndiDataAdapter getJndiDataAdapter()
	{
		return (JndiDataAdapter)getDataAdapter();
	}
	
	@Override
	public void contributeParameters(Map<String, Object> parameters) throws JRException
	{
		JndiDataAdapter jndiDataAdapter = getJndiDataAdapter();
		if (jndiDataAdapter != null)
		{

			try {
				Context ctx = new InitialContext();
				DataSource dataSource = (DataSource) ctx.lookup("java:comp/env/" + jndiDataAdapter.getDataSourceName());
				connection = dataSource.getConnection();
			}
			catch (Exception ex)
			{ 
				throw new JRException(ex);
			}

			parameters.put(JRParameter.REPORT_CONNECTION, connection);
		}
	}
	
	@Override
	public void dispose() 
	{
		if (connection != null) 
		{
			try 
			{
				connection.close();
			}
			catch (Exception ex) 
			{
				if (log.isErrorEnabled())
					log.error("Error while closing the connection.", ex);
			}
		}
	}

}
