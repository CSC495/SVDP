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
package net.sf.jasperreports.repo;

import java.io.File;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.URL;
import java.net.URLStreamHandlerFactory;

import net.sf.jasperreports.engine.DefaultJasperReportsContext;
import net.sf.jasperreports.engine.JRException;
import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JasperReportsContext;
import net.sf.jasperreports.engine.util.FileResolver;
import net.sf.jasperreports.engine.util.JRLoader;
import net.sf.jasperreports.engine.util.JRResourcesUtil;



/**
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: DefaultRepositoryService.java 5050 2012-03-12 10:11:26Z teodord $
 */
public class DefaultRepositoryService implements StreamRepositoryService
{
	/**
	 * 
	 */
	private JasperReportsContext jasperReportsContext;

	/**
	 * 
	 */
	private ClassLoader classLoader;
	private URLStreamHandlerFactory urlHandlerFactory;
	private FileResolver fileResolver;

	/**
	 * @deprecated Replaced by {@link #DefaultRepositoryService(JasperReportsContext)}.
	 */
	public DefaultRepositoryService() 
	{
		this(DefaultJasperReportsContext.getInstance());
	}
	
	/**
	 *
	 */
	public DefaultRepositoryService(JasperReportsContext jasperReportsContext) 
	{
		this.jasperReportsContext = jasperReportsContext;
	}
	
	/**
	 *
	 */
	public void setClassLoader(ClassLoader classLoader) 
	{
		this.classLoader = classLoader;
	}
	
	/**
	 *
	 */
	public void setURLStreamHandlerFactory(URLStreamHandlerFactory urlHandlerFactory) 
	{
		this.urlHandlerFactory = urlHandlerFactory;
	}
	
	/**
	 *
	 */
	public void setFileResolver(FileResolver fileResolver) 
	{
		this.fileResolver = fileResolver;
	}
	
	/**
	 * @deprecated To be removed.
	 */
	public FileResolver getFileResolver() 
	{
		return fileResolver;
	}
	
	/**
	 * @deprecated To be removed.
	 */
	public void setContext(RepositoryContext context) 
	{
	}
	
	/**
	 * @deprecated To be removed.
	 */
	public void revertContext()
	{
	}

	/**
	 * 
	 */
	public InputStream getInputStream(String uri)
	{
		try
		{
			URL url = JRResourcesUtil.createURL(uri, urlHandlerFactory);
			if (url != null)
			{
				return JRLoader.getInputStream(url);
			}

			File file = JRResourcesUtil.resolveFile(uri, fileResolver);
			if (file != null)
			{
				return JRLoader.getInputStream(file);
			}

			url = JRResourcesUtil.findClassLoaderResource(uri, classLoader);
			if (url != null)
			{
				return JRLoader.getInputStream(url);
			}
		}
		catch (JRException e)
		{
			throw new JRRuntimeException(e);
		}
		
		return null;
	}
	
	/**
	 * 
	 */
	public OutputStream getOutputStream(String uri)
	{
		throw new UnsupportedOperationException();
	}
	
	/**
	 * 
	 */
	public Resource getResource(String uri)
	{
		throw new JRRuntimeException("Not implemented.");//FIXMEREPO
	}
	
	/**
	 * 
	 */
	public void saveResource(String uri, Resource resource)
	{
		throw new UnsupportedOperationException();
	}
	
	/**
	 * 
	 */
	public <K extends Resource> K getResource(String uri, Class<K> resourceType)
	{
		PersistenceService persistenceService = PersistenceUtil.getInstance(jasperReportsContext).getService(DefaultRepositoryService.class, resourceType);
		if (persistenceService != null)
		{
			return (K)persistenceService.load(uri, this);
		}
		return null;
	}


}
