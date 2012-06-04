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

import net.sf.jasperreports.engine.JRPrintHyperlink;


/**
 * A simple hyperlink target generator that can be used to handle custom
 * hyperlink targets.
 * <p>
 * The generator produces Strings which should be used as hyperlink targets.
 * </p>
 * 
 * @author Teodor Danciu (teodord@users.sourceforge.net)
 * @version $Id: JRHyperlinkTargetProducer.java 4595 2011-09-08 15:55:10Z teodord $
 */
public interface JRHyperlinkTargetProducer
{
	
	/**
	 * Generates the String hyperlink target for a hyperlink instance.
	 *
	 * @param hyperlink the hyperlink instance
	 * @return the genereated String hyperlink target
	 */
	String getHyperlinkTarget(JRPrintHyperlink hyperlink);

}
