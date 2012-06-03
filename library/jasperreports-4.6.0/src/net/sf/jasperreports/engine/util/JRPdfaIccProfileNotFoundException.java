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

import net.sf.jasperreports.engine.JRConstants;
import net.sf.jasperreports.engine.JRRuntimeException;


/**
 * Exception raised when an ICC profile is not available to the JVM. 
 * <p/>
 * The ICC profiles are defined by the International Color Consortium. They are a means by which iText ensures color
 * management in a device-independent manner and they need to be embedded for PDF/A-1 compliance.
 * <p/>
 * Here are some sources of freely available and high quality ICC profiles:
 * <ul>
 * 	<li>sRGB ICC profiles provided by the International Color Consortium:
 *		<ul> 
 *			<li><a href="http://www.color.org/srgbprofiles.html">http://www.color.org/srgbprofiles.html</a></li>
 *		</ul>
 *	</li>
 * </li>
 * <li>RGB and CMYK ICC profiles for various printing conditions provided by the European Color Initiative (ECI):
 *		<ul>
 * 			<li><a href="http://www.eci.org/doku.php?id=en:downloads#icc_profiles_from_eci">http://www.eci.org/doku.php?id=en:downloads#icc_profiles_from_eci</a></li>
 * 		</ul>
 * </li>
 * <li>RGB and CMYK ICC profiles provided by Adobe:
 * 		<ul>
 * 			<li><a href="http://www.adobe.com/support/downloads/iccprofiles/iccprofiles_win.html">http://www.adobe.com/support/downloads/iccprofiles/iccprofiles_win.html</a></li>
 *		</ul> 
 * </li>
 * <li>Ifra profiles for newspaper publishing:
 * 		<ul>
 *			<li><a href="http://www.ifra.com/website/website.nsf/html/CONT_CONS_DL?OpenDocument&CTDL&xxx&">http://www.ifra.com/website/website.nsf/html/CONT_CONS_DL?OpenDocument&CTDL&xxx&</a></li>
 * 		</ul> 		
 * </li>
 * </ul>
 * <p/>
 * After obtaining a valid ICC profile, create the {@link net.sf.jasperreports.engine.export.JRPdfExporterParameter#PDFA_ICC_PROFILE_PATH PDFA_ICC_PROFILE_PATH}
 * pdf exporter parameter with the path to the profile.
 * 
 * @author Narcis Marcu (narcism@users.sourceforge.net)
 * @version $Id: JRPdfaIccProfileNotFoundException.java 4595 2011-09-08 15:55:10Z teodord $
 */
public class JRPdfaIccProfileNotFoundException extends JRRuntimeException
{
	private static final long serialVersionUID = JRConstants.SERIAL_VERSION_UID;


	/**
	 * 
	 */
	public JRPdfaIccProfileNotFoundException()
	{
		super("The ICC profile is not available to the JVM. See the Javadoc for more details.");
	}
}
