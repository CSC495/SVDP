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
package net.sf.jasperreports.engine.component;

/**
 * A component manager is the entry point through which the handlers for a
 * single component type can be accessed.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: ComponentManager.java 4595 2011-09-08 15:55:10Z teodord $
 * @see ComponentsEnvironment#getComponentManager(ComponentKey)
 * @see ComponentsBundle#getComponentManager(String)
 */
public interface ComponentManager
{

	/**
	 * Returns the component compiler.
	 * 
	 * @return the component compiler
	 */
	ComponentCompiler getComponentCompiler();

	/**
	 * Returns the component XML writer.
	 * 
	 * @return the component XML writer
	 */
	ComponentXmlWriter getComponentXmlWriter();
	
	/**
	 * Returns the factory of fill component instances.
	 * 
	 * @return the factory of fill component instances
	 */
	ComponentFillFactory getComponentFillFactory();

	
	/**
	 * Returns the design component preview converter.
	 * 
	 * <p>
	 * May be <code>null</code>, in which case a static icon is used when
	 * previewing a report that contains an instance of the component.
	 * 
	 * @return the design component preview converter, or <code>null</code>
	 * if no such converter exists for the component.
	 */
	ComponentDesignConverter getDesignConverter();
	
}
