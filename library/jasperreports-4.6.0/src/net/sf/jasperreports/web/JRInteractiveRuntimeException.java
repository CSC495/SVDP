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
import net.sf.jasperreports.engine.JRRuntimeException;


/**
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JRInteractiveRuntimeException.java 5330 2012-05-03 10:08:37Z teodord $
 */
public class JRInteractiveRuntimeException extends JRRuntimeException
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;

	public JRInteractiveRuntimeException(String message) {
		super(message);
	}
	
	public JRInteractiveRuntimeException(String message, Throwable cause) {
		super(message, cause);
	}

	public JRInteractiveRuntimeException(Throwable cause) {
		super(cause);
	}
}
