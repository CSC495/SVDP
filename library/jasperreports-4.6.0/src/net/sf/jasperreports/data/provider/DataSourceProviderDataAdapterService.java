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
package net.sf.jasperreports.data.provider;

import java.util.Map;

import net.sf.jasperreports.data.AbstractClasspathAwareDataAdapterService;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRDataSourceProvider;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.util.CompositeClassloader;
import net.sf.jasperreports.engine.util.JRClassLoader;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: DataSourceProviderDataAdapterService.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class DataSourceProviderDataAdapterService extends AbstractClasspathAwareDataAdapterService 
{
	private JRDataSourceProvider provider = null;

	/**
	 * 
	 */
	public DataSourceProviderDataAdapterService(
		JasperReportsContext jasperReportsContext,
		DataSourceProviderDataAdapter dsDataAdapter
		) 
	{
		super(jasperReportsContext, dsDataAdapter);
	}

	/**
	 * @deprecated Replaced by {@link #DataSourceProviderDataAdapterService(JasperReportsContext, DataSourceProviderDataAdapter)}.
	 */
	public DataSourceProviderDataAdapterService(DataSourceProviderDataAdapter dsDataAdapter) 
	{
		this(DefaultJasperReportsContext.getInstance(), dsDataAdapter);
	}

	public DataSourceProviderDataAdapter getDataSourceProviderDataAdapter() {
		return (DataSourceProviderDataAdapter) getDataAdapter();
	}
	
	public JRDataSourceProvider getProvider() throws JRException
	{
		if (provider == null)
		{
			DataSourceProviderDataAdapter dsDataAdapter = getDataSourceProviderDataAdapter();
			if (dsDataAdapter != null) 
			{
				ClassLoader oldThreadClassLoader = Thread.currentThread().getContextClassLoader();

				try 
				{
					Thread.currentThread().setContextClassLoader(
						new CompositeClassloader(getClassLoader(), oldThreadClassLoader)
						);

					Class<?> clazz = JRClassLoader.loadClassForRealName(dsDataAdapter.getProviderClass());
					provider = (JRDataSourceProvider) clazz.newInstance();
					// FIXME: I don't have a report, why I need a report??!
				} catch (ClassNotFoundException e) {
					throw new JRException(e);
				} catch (IllegalAccessException e) {
					throw new JRException(e);
				} catch (InstantiationException e) {
					throw new JRException(e);
				}
				finally
				{
					Thread.currentThread().setContextClassLoader(oldThreadClassLoader);
				}
			}
		}
		
		return provider;
	}

	@Override
	public void contributeParameters(Map<String, Object> parameters) throws JRException 
	{
		JRDataSourceProvider dsProvider = getProvider();
		if (dsProvider != null) 
		{
			parameters.put(JRParameter.REPORT_DATA_SOURCE, dsProvider.create(null));
		}
	}

}
