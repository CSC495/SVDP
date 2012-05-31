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
package net.sf.jasperreports.web;

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRException;

/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JRInteractiveException.java 5330 2012-05-03 10:08:37Z teodord $
 */
public class JRInteractiveException extends JRException
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	public JRInteractiveException(String message)
	{
		super(message);
	}

	public JRInteractiveException(Throwable t)
	{
		super(t);
	}

	public JRInteractiveException(String message, Throwable t)
	{
		super(message, t);
	}
}
