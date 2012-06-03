/*
 * JasperReports - Free Java Reporting Library.
 * Copyright (C) 2005 - 2011 Works, Inc. All rights reserved.
 * http://www.works.com
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

/*
 * Licensed to Jaspersoft Corporation under a Contributer Agreement
 */
package net.sf.jasperreports.engine.fill;

import java.io.IOException;
import java.io.InputStream;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.ObjectStreamClass;
import java.io.OutputStream;
import java.lang.ref.ReferenceQueue;
import java.lang.ref.WeakReference;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedHashMap;
import java.util.List;
import java.util.Map;
import java.util.Map.Entry;

import net.sf.jasperreports.engine.JRRuntimeException;
import net.sf.jasperreports.engine.JRVirtualizable;
import net.sf.jasperreports.engine.JRVirtualizer;

import org.apache.commons.collections.ReferenceMap;
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;


/**
 * Abstract base for LRU and serialization based virtualizer
 *
 * @author John Bindel
 * @version $Id: JRAbstractLRUVirtualizer.java 5180 2012-03-29 13:23:12Z teodord $
 */
public abstract class JRAbstractLRUVirtualizer implements JRVirtualizer
{
	private static final Log log = LogFactory.getLog(JRAbstractLRUVirtualizer.class);

	protected static class CacheReference extends WeakReference<JRVirtualizable>
	{
		private final String id;

		public CacheReference(JRVirtualizable o, ReferenceQueue<JRVirtualizable> queue)
		{
			super(o, queue);
			id = o.getUID();
		}

		public String getId()
		{
			return id;
		}
	}

	/**
	 * This class keeps track of how many objects are currently in memory, and
	 * when there are too many, it pushes the last touched one to disk.
	 */
	protected class Cache
	{
		private final int maxSize;
		private final ReferenceQueue<JRVirtualizable> refQueue;
		private final LinkedHashMap<String, CacheReference> map;

		Cache(int maxSize)
		{
			this.maxSize = maxSize;
			map = new LinkedHashMap<String, CacheReference>(16, 0.75f, true);
			refQueue = new ReferenceQueue<JRVirtualizable>();
		}

		protected JRVirtualizable getMapValue(CacheReference val)
		{
			JRVirtualizable o;
			if (val == null)
			{
				o = null;
			}
			else
			{
				if (val.isEnqueued())
				{
					o = null;
				}
				else
				{
					o = val.get();
				}
			}
			return o;
		}

		protected CacheReference toMapValue(JRVirtualizable val)
		{
			return val == null ? null : new CacheReference(val, refQueue);
		}

		protected void purge()
		{
			CacheReference ref;
			while ((ref = (CacheReference) refQueue.poll()) != null)
			{
				map.remove(ref.getId());
			}
		}

		public boolean contains(String id)
		{
			purge();
			
			return map.containsKey(id);
		}
		
		public JRVirtualizable get(String id)
		{
			purge();

			return getMapValue(map.get(id));
		}

		public JRVirtualizable put(String id, JRVirtualizable o)
		{
			purge();

			return getMapValue(map.put(id, toMapValue(o)));
		}

		public List<JRVirtualizable> evictionCandidates()
		{
			if (map.size() <= maxSize)
			{
				return Collections.<JRVirtualizable>emptyList();
			}
			
			int candidateCount = map.size() - maxSize;
			List<JRVirtualizable> candidates = new ArrayList<JRVirtualizable>();
			Iterator<Entry<String, CacheReference>> mapIterator = map.entrySet().iterator();
			while (candidates.size() < candidateCount && mapIterator.hasNext())
			{
				Entry<String, CacheReference> entry = mapIterator.next();
				JRVirtualizable value = getMapValue(entry.getValue());
				
				if (value == null)
				{
					// this entry will get removed by purge()
					--candidateCount;
				}
				else if (isEvictable(value))
				{
					if (log.isDebugEnabled())
					{
						log.debug("LRU eviction candidate: " + entry.getKey());
					}
					
					candidates.add(value);
				}
			}

			if (candidates.size() < candidateCount)
			{
				log.debug("The virtualizer is used by more contexts than its in-memory cache size " + maxSize);
			}
			
			return candidates;
		}
		
		public JRVirtualizable remove(String id)
		{
			purge();

			return getMapValue(map.remove(id));
		}

		public Iterator<String> idIterator()
		{
			purge();

			final Iterator<CacheReference> valsIt = map.values().iterator();
			return new Iterator<String>()
			{
				public boolean hasNext()
				{
					return valsIt.hasNext();
				}

				public String next()
				{
					CacheReference ref = valsIt.next();
					return ref.getId();
				}

				public void remove()
				{
					valsIt.remove();
				}
			};
		}
	}

	protected static final int CLASSLOADER_IDX_NOT_SET = -1;

	protected static boolean isAncestorClassLoader(ClassLoader loader)
	{
		for (
				ClassLoader ancestor = JRAbstractLRUVirtualizer.class.getClassLoader();
				ancestor != null;
				ancestor = ancestor.getParent())
		{
			if (ancestor.equals(loader))
			{
				return true;
			}
		}
		return false;
	}

	protected final Map<ClassLoader,Integer> classLoadersIndexes = new HashMap<ClassLoader,Integer>();
	protected final List<ClassLoader> classLoadersList = new ArrayList<ClassLoader>();

	protected class ClassLoaderAnnotationObjectOutputStream extends VirtualizationObjectOutputStream
	{
		public ClassLoaderAnnotationObjectOutputStream(OutputStream out, 
				JRVirtualizationContext virtualizationContext) throws IOException
		{
			super(out, virtualizationContext);
		}

		protected void annotateClass(Class<?> clazz) throws IOException
		{
			super.annotateClass(clazz);

			ClassLoader classLoader = clazz.getClassLoader();
			int loaderIdx;
			if (clazz.isPrimitive()
					|| classLoader == null
					|| isAncestorClassLoader(classLoader))
			{
				loaderIdx = CLASSLOADER_IDX_NOT_SET;
			}
			else
			{
				Integer idx = classLoadersIndexes.get(classLoader);
				if (idx == null)
				{
					idx = Integer.valueOf(classLoadersList.size());
					classLoadersIndexes.put(classLoader, idx);
					classLoadersList.add(classLoader);
				}
				loaderIdx = idx.intValue();
			}

			writeShort(loaderIdx);
		}
	}

	protected class ClassLoaderAnnotationObjectInputStream extends VirtualizationObjectInputStream
	{
		public ClassLoaderAnnotationObjectInputStream(InputStream in, 
				JRVirtualizationContext virtualizationContext) throws IOException
		{
			super(in, virtualizationContext);
		}

		protected Class<?> resolveClass(ObjectStreamClass desc) throws IOException, ClassNotFoundException
		{
			Class<?> clazz;
			try
			{
				clazz = super.resolveClass(desc);
				readShort();
			}
			catch (ClassNotFoundException e)
			{
				int loaderIdx = readShort();
				if (loaderIdx == CLASSLOADER_IDX_NOT_SET)
				{
					throw e;
				}

				ClassLoader loader = classLoadersList.get(loaderIdx);
				clazz = Class.forName(desc.getName(), false, loader);
			}

			return clazz;
		}


	}

	private final Cache pagedIn;

	private final ReferenceMap pagedOut;

	protected volatile JRVirtualizable lastObject;
	protected ReferenceMap lastObjectMap;
	protected ReferenceMap lastObjectSet;

	private boolean readOnly;

	/**
	 * @param maxSize
	 *            the maximum size (in JRVirtualizable objects) of the paged in
	 *            cache.
	 */
	protected JRAbstractLRUVirtualizer(int maxSize)
	{
		this.pagedIn = new Cache(maxSize);
		this.pagedOut = new ReferenceMap(ReferenceMap.HARD, ReferenceMap.WEAK);
		this.lastObject = null;

		this.lastObjectMap = new ReferenceMap(ReferenceMap.WEAK, ReferenceMap.WEAK);
		this.lastObjectSet = new ReferenceMap(ReferenceMap.WEAK, ReferenceMap.HARD);
	}

	protected synchronized final boolean isPagedOut(String id)
	{
		return pagedOut.containsKey(id);
	}

	protected synchronized boolean isPagedOutAndTouch(JRVirtualizable o, String uid)
	{
		boolean virtualized = isPagedOut(uid);
		if (!virtualized)
		{
			touch(o);
		}
		return virtualized;
	}

	protected final void setLastObject(JRVirtualizable o)
	{
		if (o != null && lastObject != o)
		{
			// lastObject is mostly an optimization, we don't care if we don't have atomic operations here
			this.lastObject = o;
			
			synchronized (this)
			{
				JRVirtualizationContext context = o.getContext();
				Object ownerLast = lastObjectMap.get(context);
				if (ownerLast != o)
				{
					if (ownerLast != null)
					{
						lastObjectSet.remove(ownerLast);
					}
					lastObjectMap.put(context, o);
					lastObjectSet.put(o, Boolean.TRUE);
				}
			}
		}
	}

	/**
	 * Sets the read only mode for the virtualizer.
	 * <p/>
	 * When in read-only mode, the virtualizer assumes that virtualizable objects are final
	 * and any change in a virtualizable object's data is discarded.
	 * <p/>
	 * When the virtualizer is used for multiple virtualization contexts (in shared mode),
	 * calling this method would override the read-only flags from all the contexts and all the
	 * objects will be manipulated in read-only mode.
	 * Use {@link JRVirtualizationContext#setReadOnly(boolean) JRVirtualizationContext.setReadOnly(boolean)}
	 * to set the read-only mode for one specific context.
	 *
	 * @param ro the read-only mode to set
	 */
	public void setReadOnly(boolean ro)
	{
		this.readOnly = ro;
	}


	/**
	 * Determines whether the virtualizer is in read-only mode.
	 *
	 * @return whether the virtualizer is in read-only mode
	 * @see #setReadOnly(boolean)
	 */
	public boolean isReadOnly()
	{
		return readOnly;
	}

	protected final boolean isReadOnly(JRVirtualizable o)
	{
		return readOnly || o.getContext().isReadOnly();
	}

	public void registerObject(JRVirtualizable o)
	{
		if (log.isDebugEnabled())
		{
			log.debug("registering " + o.getUID());
		}
		
		synchronized (this)
		{
			setLastObject(o);
			JRVirtualizable old = pagedIn.put(o.getUID(), o);
			if (old != null && old != o)
			{
				pagedIn.put(o.getUID(), old);
				throw new IllegalStateException("Wrong object stored with UID \"" + o.getUID() + "\"");
			}
		}
		
		if (log.isDebugEnabled())
		{
			log.debug("registered object " + o + " with id " + o.getUID());
		}
		
		evict();
	}

	protected boolean isEvictable(JRVirtualizable value)
	{
		return value.getContext().isDisposed() || !lastObjectSet.containsKey(value);
	}

	protected void evict()
	{
		//FIXME lucianc also attempt to evict on non-put operations if the last evict was not successful 
		//FIXME lucianc prevent two threads from attempting to evict the same objects 
		
		List<JRVirtualizable> candidates;
		synchronized (this)
		{
			candidates = pagedIn.evictionCandidates();
		}
		
		for (JRVirtualizable o : candidates)
		{
			String uid = o.getUID();
			if (o.getContext().tryLock())
			{
				try
				{
					boolean evictable;
					synchronized (this)
					{
						// check again due to sequential locking
						evictable = pagedIn.contains(uid) && isEvictable(o);
						if (evictable)
						{
							pagedIn.remove(uid);
						}
					}
					
					if (evictable)
					{
						if (log.isDebugEnabled())
						{
							log.debug("evicting " + uid);
						}
						
						//FIXME lucianc if the context is disposed, this is not needed
						virtualizeData(o);
					}
					else
					{
						if (log.isDebugEnabled())
						{
							log.debug("no longer evictable: " + uid);
						}
					}
				}
				finally
				{
					o.getContext().unlock();
				}
			}
			else
			{
				if (log.isDebugEnabled())
				{
					log.debug("couldn't lock for eviction " + uid);
				}
			}
		}
	}

	public void deregisterObject(JRVirtualizable o)
	{
		String uid = o.getUID();

		if (log.isDebugEnabled())
		{
			log.debug("deregistering " + uid);
		}
		
		//try to remove virtual data
		try
		{
			dispose(o.getUID());
		}
		catch (Exception e)
		{
			log.error("Error removing virtual data", e);
			//ignore
		}

		synchronized(this)
		{
			JRVirtualizable oldIn = pagedIn.remove(uid);
			if (oldIn != null)
			{
				if (oldIn != o)
				{
					pagedIn.put(uid, oldIn);
					throw new IllegalStateException("Wrong object stored with UID \"" + o.getUID() + "\"");
				}
				
				Object contextLast = lastObjectMap.get(o.getContext());
				if (contextLast == o)
				{
					lastObjectMap.remove(o.getContext());
					lastObjectSet.remove(o);
				}
			}
			else
			{
				Object oldOut = pagedOut.remove(uid);
				if (oldOut != null && oldOut != o)
				{
					pagedOut.put(uid, oldOut);
					throw new IllegalStateException("Wrong object stored with UID \"" + o.getUID() + "\"");
				}
			}

			// We don't really care if someone deregisters an object
			// that's not registered.
		}
		
		if (log.isDebugEnabled())
		{
			log.debug("deregistered object " + o + " with id " + o.getUID());
		}
	}

	public void touch(JRVirtualizable o)
	{
		// If we just touched this object, don't touch it again.
		if (this.lastObject != o)
		{
			//FIXME lucianc this doesn't scale well with concurrency
			// get the object from the map to update LRU order
			JRVirtualizable internalObject;
			synchronized (this)
			{
				internalObject = pagedIn.get(o.getUID());
			}
			
			setLastObject(internalObject);
		}
	}

	public void requestData(JRVirtualizable o)
	{
		String uid = o.getUID();
		boolean evictRequired = false;
		
		o.getContext().lock();
		try
		{
			if (isPagedOutAndTouch(o, uid))
			{
				if (log.isDebugEnabled())
				{
					log.debug("internalizing " + uid);
				}
				
				// unvirtualize
				try
				{
					pageIn(o);
				}
				catch (IOException e)
				{
					log.error("Error devirtualizing object", e);
					throw new JRRuntimeException(e);
				}

				synchronized (this)
				{
					setLastObject(o);
					pagedOut.remove(uid);
					pagedIn.put(uid, o);
				}

				o.afterInternalization();
				
				evictRequired = true;
			}
		}
		finally
		{
			o.getContext().unlock();
		}
		
		if (evictRequired)
		{
			evict();
		}
	}

	public void clearData(JRVirtualizable o)
	{
		String uid = o.getUID();
		if (isPagedOutAndTouch(o, uid))
		{
			// remove virtual data
			dispose(uid);

			synchronized (this)
			{
				pagedOut.remove(uid);
			}
		}
	}

	public void virtualizeData(JRVirtualizable o)
	{
		String uid = o.getUID();
		if (!isPagedOut(uid))
		{
			if (log.isDebugEnabled())
			{
				log.debug("externalizing " + uid);
			}
			
			o.beforeExternalization();

			// virtualize
			try
			{
				pageOut(o);
			}
			catch (IOException e)
			{
				log.error("Error virtualizing object", e);
				throw new JRRuntimeException(e);
			}

			o.afterExternalization();

			// Wait until we know it worked before tossing the data.
			o.removeVirtualData();

			synchronized (this)
			{
				pagedOut.put(uid, o);
			}
		}
	}

	protected void finalize() throws Throwable //NOSONAR
	{
		cleanup();

		super.finalize();
	}

	/**
	 * Writes serialized indentity and virtual data of a virtualizable object to a stream.
	 *
	 * @param o the serialized object
	 * @param out the output stream
	 * @throws JRRuntimeException
	 */
	protected final void writeData(JRVirtualizable o, OutputStream out)
			throws JRRuntimeException
	{
		try
		{
			ObjectOutputStream oos = new ClassLoaderAnnotationObjectOutputStream(out, o.getContext());
			oos.writeObject(o.getVirtualData());
			oos.flush();
		}
		catch (IOException e)
		{
			log.error("Error virtualizing object", e);
			throw new JRRuntimeException(e);
		}
	}


	/**
	 * Reads serialized identity and virtual data for a virtualizable object
	 * from a stream.
	 *
	 * @param o the virtualizable object
	 * @param in the input stream
	 * @throws JRRuntimeException
	 */
	protected final void readData(JRVirtualizable o, InputStream in)
			throws JRRuntimeException
	{
		try
		{
			ObjectInputStream ois = new ClassLoaderAnnotationObjectInputStream(in, o.getContext());
			o.setVirtualData(ois.readObject());
		}
		catch (IOException e)
		{
			log.error("Error devirtualizing object", e);
			throw new JRRuntimeException(e);
		}
		catch (ClassNotFoundException e)
		{
			log.error("Error devirtualizing object", e);
			throw new JRRuntimeException(e);
		}
	}

	protected synchronized void reset()
	{
		readOnly = false;
	}

	protected final void disposeAll()
	{
		// Remove all paged-out swap files.
		for (Iterator<String> it = pagedOut.keySet().iterator(); it.hasNext();)
		{
			String id = it.next();
			try
			{
				dispose(id);
				it.remove();
			}
			catch (Exception e)
			{
				log.error("Error cleaning up virtualizer.", e);
				// Do nothing because we want to try to remove all swap files.
			}
		}

		for (Iterator<String> it = pagedIn.idIterator(); it.hasNext();)
		{
			String id = it.next();
			try
			{
				dispose(id);
				it.remove();
			}
			catch (Exception e)
			{
				log.error("Error cleaning up virtualizer.", e);
				// Do nothing because we want to try to remove all swap files.
			}
		}
	}

	/**
	 * Writes a virtualizable object's data to an external storage.
	 *
	 * @param o a virtualizable object
	 * @throws IOException
	 */
	protected abstract void pageOut(JRVirtualizable o) throws IOException;


	/**
	 * Reads a virtualizable object's data from an external storage.
	 *
	 * @param o a virtualizable object
	 * @throws IOException
	 */
	protected abstract void pageIn(JRVirtualizable o) throws IOException;


	/**
	 * Removes the external data associated with a virtualizable object.
	 *
	 * @param virtualId the ID of the virtualizable object
	 */
	protected abstract void dispose(String virtualId);
}
