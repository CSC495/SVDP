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
package net.sf.jasperreports.engine.util;

/**
 * A composite classloader that has a parent classloader and an alternate
 * classloader.
 * 
 * <p>
 * When resolving classes, the parent classloader is consulted first, and if
 * that classloader cannot find the class, the alternate/second classloader is
 * asked to resolve the class. 
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: CompositeClassloader.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class CompositeClassloader extends ClassLoader
{

	private final ClassLoader fallback;

	/**
	 * Creates a composite classloader.
	 * 
	 * @param parent the parent classloader
	 * @param fallback the alternate classloader
	 */
	public CompositeClassloader(ClassLoader parent, ClassLoader fallback)
	{
		super(parent);
		
		this.fallback = fallback;
	}

	protected Class<?> findClass(String name) throws ClassNotFoundException
	{
		// this method is only called when the parent classloader cannot
		// resolve the class, so we try the alternate classloder
		
		if (fallback != null)
		{
			return fallback.loadClass(name);
		}
		
		// throw ClassNotFoundException
		return super.findClass(name);
	}
	
}
