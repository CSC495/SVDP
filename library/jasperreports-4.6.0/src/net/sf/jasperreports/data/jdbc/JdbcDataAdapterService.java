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
package net.sf.jasperreports.data.jdbc;

import java.sql.Connection;
import java.sql.Driver;
import java.sql.SQLException;
import java.util.Map;
import java.util.Properties;

import net.sf.jasperreports.data.AbstractClasspathAwareDataAdapterService;
import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRParameter;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.util.CompositeClassloader;
import net.sf.jasperreports.engine.util.JRClassLoader;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JdbcDataAdapterService.java 5368 2012-05-10 18:34:15Z chicuslavic $
 */
public class JdbcDataAdapterService extends AbstractClasspathAwareDataAdapterService 
{
	private static final Log log = LogFactory.getLog(JdbcDataAdapterService.class);
	
	private Connection connection = null; 

//	/**
//	 * This classloader is used to load JDBC drivers available in the set of
//	 * paths provided by classpathPaths.
//	 */
//	private ClassLoader classLoader = null;
//
//	/**
//	 * Same as getDriversClassLoader(false)
//	 * 
//	 * @return
//	 */
//	public ClassLoader getClassLoader() {
//		return getClassLoader(false);
//	}
//
//	/**
//	 * Return the classloader, an URLClassLoader made up with all the paths
//	 * defined to look for Drivers (mainly jars).
//	 * 
//	 * @param reload
//	 *            - if true, it forces a classloader rebuilt with the set of
//	 *            paths in classpathPaths.
//	 * @return
//	 */
//	public ClassLoader getClassLoader(boolean reload) {
//		if (classLoader == null || reload) {
//			List<String> paths = ((JdbcDataAdapter) getDataAdapter())
//					.getClasspathPaths();
//			List<URL> urls = new ArrayList<URL>();
//			for (String p : paths) {
//				FileResolver fileResolver = JRResourcesUtil
//						.getFileResolver(null);
//				File f = fileResolver.resolveFile(p);
//
//				if (f != null && f.exists()) {
//					try {
//						urls.add(f.toURI().toURL());
//					} catch (MalformedURLException e) {
//						// e.printStackTrace();
//						// We don't care if the entry cannot be found.
//					}
//				}
//			}
//
//			classLoader = new URLClassLoader(urls.toArray(new URL[urls.size()]));
//		}
//
//		return classLoader;
//	}

	/**
	 * 
	 */
	public JdbcDataAdapterService(JasperReportsContext jasperReportsContext, JdbcDataAdapter jdbcDataAdapter) 
	{
		super(jasperReportsContext, jdbcDataAdapter);
	}

	/**
	 * @deprecated Replaced by {@link #JdbcDataAdapterService(JasperReportsContext, JdbcDataAdapter)}.
	 */
	public JdbcDataAdapterService(JdbcDataAdapter jdbcDataAdapter) 
	{
		this(DefaultJasperReportsContext.getInstance(), jdbcDataAdapter);
	}

	public JdbcDataAdapter getJdbcDataAdapter() {
		return (JdbcDataAdapter) getDataAdapter();
	}

	@Override
	public void contributeParameters(Map<String, Object> parameters) throws JRException 
	{
		JdbcDataAdapter jdbcDataAdapter = getJdbcDataAdapter();
		if (jdbcDataAdapter != null) 
		{
			ClassLoader oldThreadClassLoader = Thread.currentThread().getContextClassLoader();

			try 
			{
				Thread.currentThread().setContextClassLoader(
					new CompositeClassloader(getClassLoader(), oldThreadClassLoader)
					);
				
				Class<?> clazz = JRClassLoader.loadClassForRealName(jdbcDataAdapter.getDriver());
				Driver driver = (Driver) clazz.newInstance();
				
//				Driver driver = (Driver) (Class.forName(
//						jdbcDataAdapter.getDriver(), true, getClassLoader()))
//						.newInstance();

				
				Properties	connectProps = new Properties();
				Map<String, String> map = jdbcDataAdapter.getProperties();
				if(map != null)
					for(String key: map.keySet())
						connectProps.setProperty(key, map.get(key));
				

				String password = jdbcDataAdapter.getPassword();
				if (!jdbcDataAdapter.isSavePassword()) {
					password = getPassword();
				}

				connectProps.setProperty("user", jdbcDataAdapter.getUsername());
				connectProps.setProperty("password", password);
				
				connection = driver.connect(jdbcDataAdapter.getUrl(), connectProps);
				if(connection == null)
					throw new SQLException("No suitable driver found for "+ jdbcDataAdapter.getUrl());
			}
			catch (Exception ex)
			{
				throw new JRException(ex);
			}
			finally
			{
				Thread.currentThread().setContextClassLoader(oldThreadClassLoader);
			}

			parameters.put(JRParameter.REPORT_CONNECTION, connection);
		}
	}

	public String getPassword() throws JRException {
		throw new JRException(
				"This service implementation needs the password to be saved in the data adapter.");
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
