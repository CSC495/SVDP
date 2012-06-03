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

import java.io.UnsupportedEncodingException;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

import net.sf.jasperreports.engine.JRRuntimeException;

/**
 * @author Lucian Chirita (lucianc@users.sourceforge.net)
 * @version $Id: DigestUtils.java 5337 2012-05-04 09:15:58Z lucianc $
 */
public final class DigestUtils
{

	private static final DigestUtils INSTANCE = new DigestUtils();
	
	public static DigestUtils instance()
	{
		return INSTANCE;
	}
	
	private DigestUtils()
	{
	}
	
	public MD5Digest md5(String text)
	{
		try
		{
			MessageDigest digest = MessageDigest.getInstance("MD5");
			byte[] digestBytes = digest.digest(text.getBytes("UTF-8"));
			long low = (long) (digestBytes[0] &0xFF) << 56
					| (long) (digestBytes[1] &0xFF) << 48
					| (long) (digestBytes[2] &0xFF) << 40
					| (long) (digestBytes[3] &0xFF) << 32
					| (long) (digestBytes[4] &0xFF) << 24
					| (long) (digestBytes[5] &0xFF) << 16
					| (long) (digestBytes[6] &0xFF) << 8
					| (long) (digestBytes[7] &0xFF) << 0;
			long high = (long) (digestBytes[8] &0xFF) << 56
					| (long) (digestBytes[9] &0xFF) << 48
					| (long) (digestBytes[10] &0xFF) << 40
					| (long) (digestBytes[11] &0xFF) << 32
					| (long) (digestBytes[12] &0xFF) << 24
					| (long) (digestBytes[13] &0xFF) << 16
					| (long) (digestBytes[14] &0xFF) << 8
					| (long) (digestBytes[15] &0xFF) << 0;
			return new MD5Digest(low, high);
		}
		catch (NoSuchAlgorithmException e)
		{
			throw new JRRuntimeException("MD5 digest not available", e);
		}
		catch (UnsupportedEncodingException e)
		{
			// should not happen
			throw new JRRuntimeException(e);
		}
	}
	
}
