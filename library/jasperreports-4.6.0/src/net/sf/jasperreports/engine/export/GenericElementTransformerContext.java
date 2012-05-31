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
package net.sf.jasperreports.engine.export;

import net.sf.jasperreports.engine.JasperPrint;
import net.sf.jasperreports.engine.JasperReportsContext;

/**
 * A context in which an element tranformation takes place.
 * 
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: GenericElementTransformerContext.java 5390 2012-05-18 06:58:47Z lucianc $
 * @see GenericElementTransformer
 */
public interface GenericElementTransformerContext
{

	JasperReportsContext getJasperReportsContext();
	
	/**
	 * Returns the report which is currently processed.
	 * 
	 * @return currently processed report
	 */
	JasperPrint getReport();

}
